<?php

namespace AppBundle\Controller;

use AppBundle\Entity\School;
use AppBundle\Entity\SurveySchoolAnswered;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Survey;
use AppBundle\Entity\SurveyAnswer;
use AppBundle\Entity\SurveySchema;
use AppBundle\Form\Type\SurveySchemaType;
use AppBundle\Form\Type\SurveyAnswerType;
use AppBundle\Form\Type\SurveyType;
use AppBundle\Form\Type\SurveyExecuteType;

/**
 * InterviewController is the controller responsible for survey actions,
 * such as showing, assigning and conducting surveys.
 *
 * @package AppBundle\Controller
 */
class SurveyController extends Controller
{
    /**
     * Shows the given survey.
     * @param Request $request
     * @param Survey $survey
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, Survey $survey)
    {

        $department = $survey->getSemester()->getDepartment();
        $oldAns = sizeof($survey->getSurveyAnswers());
        foreach($survey->getSurveyQuestions() as $surveyQuestion){
            $answer = new SurveyAnswer();
            $answer->setSurvey($survey);
            $answer->setSurveyQuestion($surveyQuestion);

            $survey->addSurveyAnswer($answer);
        }

        $form = $this->createForm(new SurveyExecuteType(), $survey);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $schoolAnswered = new SurveySchoolAnswered();
            $schoolAnswered->setSurvey($survey);
            $schoolAnswered->setSchool($survey->getSchool());
            $sql = "
                INSERT INTO survey_schools_answered(survey_id, school_id)
                VALUES (?,?)
            ";
            $conn = $this->getDoctrine()->getManager()->getConnection();
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(1,$survey->getId());
            $stmt->bindValue(2,$survey->getSchool()->getId());
            $stmt->execute();

            $schoolAnsweredId = $conn->lastInsertId();

            $answers = $survey->getSurveyAnswers();
            $new_answer = false;
            for($i = $oldAns; $i < sizeof($answers); $i++){
                $question_id = $answers[$i]->getSurveyQuestion()->getId();
                $school_id = $survey->getSchool()->getId();
                $survey_id = $answers[$i]->getSurvey()->getId();
                $answer = $answers[$i]->getAnswer();
                if(strlen($answer)!=0){
                    $sql = "
                        INSERT INTO survey_answer(question_id, school_id, survey_id, answer, schoolAnswered_id)
                        VALUES (?,?,?,?,?);
                    ";
                    $stmt = $this->getDoctrine()->getManager()->getConnection()->prepare($sql);
                    $stmt->bindValue(1, $question_id);
                    $stmt->bindValue(2, $school_id);
                    $stmt->bindValue(3, $survey_id);
                    $stmt->bindValue(4, $answer);
                    $stmt->bindValue(5, $schoolAnsweredId);
                    $stmt->execute();
                    $new_answer = true;
                }
            }

            if($new_answer){
                $this->addFlash('undersokelse-notice','Tusen takk for ditt svar!');
                //New form without previous answers
                return $this->redirect($this->generateUrl('survey_show',array('id' => $survey->getId())));
            }
        }
        return $this->render('survey/takeSurvey.html.twig', array(
            'oldAns' => $oldAns,
            'form' => $form->createView(),

        ));
    }

    public function createSurveyAction(Request $request)
    {
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException();
        }
        $survey = new Survey();

        $form = $this->createForm(new SurveyType(), $survey);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($survey);
            $em->flush();

            // Need some form of redirect. Will cause wrong database entries if the form is rendered again
            // after a valid submit, without remaking the form with up to date question objects from the database.
            return $this->redirect($this->generateUrl('surveys'));
        }

        return $this->render('survey/survey_create.html.twig', array('form' => $form->createView()));
    }

    public function copySurvey(Request $request){
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException();
        }
    }

    public function showSurveysAction()
    {
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException();
        }
        $surveys = $this->getDoctrine()->getRepository('AppBundle:Survey')->findAll();
        foreach($surveys as $survey){
            $sql = "
                  SELECT COUNT(id)
                  FROM survey_schools_answered
                  WHERE survey_id = ?";
            $stmt = $this->getDoctrine()->getManager()->getConnection()->prepare($sql);
            $stmt->bindValue(1,$survey->getId());
            $stmt->execute();
            $result = $stmt->fetch();
            $survey->setTotalAnswered($result["COUNT(id)"]);
        }
        return $this->render('survey/surveys.html.twig', array('surveys' => $surveys));
    }

    public function editSurveyAction(Request $request, Survey $survey)
    {
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(new SurveyType(), $survey);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($survey);
            $em->flush();

            // Need some form of redirect. Will cause wrong database entries if the form is rendered again
            // after a valid submit, without remaking the form with up to date question objects from the database.
            return $this->redirect($this->generateUrl('surveys'));
        }

        return $this->render('survey/survey_create.html.twig', array('form' => $form->createView()));
    }

    /**
     * Deletes the given interview schema.
     * This method is intended to be called by an Ajax request.
     *
     * @param Survey $survey
     * @return JsonResponse
     */
    public function deleteSurveyAction(Survey $survey)
    {
        try {
            if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($survey);
                $em->flush();

                $response['success'] = true;
            } else {
                $response['success'] = false;
                $response['cause'] = 'Ikke tilstrekkelig rettigheter';
            }
        } catch (\Exception $e) {
            $response = ['success' => false,
                'code' => $e->getCode(),
                'cause' => 'Det oppstod en feil.'
            ];
        }

        return new JsonResponse($response);
    }

}

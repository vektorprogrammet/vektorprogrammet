<?php

namespace AppBundle\Controller;

use AppBundle\Entity\SurveyQuestion;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Survey;
use AppBundle\Entity\SurveyAnswer;
use AppBundle\Entity\SurveyTaken;
use AppBundle\Form\Type\SurveyType;
use AppBundle\Form\Type\SurveyExecuteType;
use SaadTazi\GChartBundle\DataTable\DataTable;

/**
 * SurveyController is the controller responsible for survey actions,
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
        $surveyTaken = new SurveyTaken();
        $surveyTaken->setSurvey($survey);
        foreach($survey->getSurveyQuestions() as $surveyQuestion){
            $answer = new SurveyAnswer();
            $answer->setSurveyQuestion($surveyQuestion);
            $answer->setSurveyTaken($surveyTaken);

            $surveyTaken->addSurveyAnswer($answer);
        }

        $form = $this->createForm(new SurveyExecuteType(), $surveyTaken);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $surveyTaken->removeNullAnswers();
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($surveyTaken);
            $em->flush();
            $new_answer = true;
            if($new_answer){
                $this->addFlash('undersokelse-notice','Tusen takk for ditt svar!');
                //New form without previous answers
                return $this->redirect($this->generateUrl('survey_show',array('id' => $survey->getId())));
            }
        }
        return $this->render('survey/takeSurvey.html.twig', array(
            'form' => $form->createView(),

        ));
    }

    public function showAdminAction(Request $request, Survey $survey)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $surveyTaken = new SurveyTaken();
        $surveyTaken->setSurvey($survey);
        foreach($survey->getSurveyQuestions() as $surveyQuestion){
            $answer = new SurveyAnswer();
            $answer->setSurveyQuestion($surveyQuestion);
            $answer->setSurveyTaken($surveyTaken);

            $surveyTaken->addSurveyAnswer($answer);
        }

        $form = $this->createForm(new SurveyExecuteType(), $surveyTaken);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $surveyTaken->removeNullAnswers();
            $em->persist($surveyTaken);
            $em->flush();
            $new_answer = true;
            if($new_answer){
                $this->addFlash('undersokelse-notice','Mottatt svar!');
                //New form without previous answers
                return $this->redirect($this->generateUrl('survey_show_admin',array('id' => $survey->getId())));
            }
        }

        $allTakenSurveys = $em->getRepository('AppBundle:SurveyTaken')->findAllTakenBySurvey($survey);
        if(count($allTakenSurveys)){
            $countAnswer = array();
            foreach($allTakenSurveys as $takenSurvey){
                foreach($takenSurvey->getSurveyAnswers() as $answer){
                    if( (!($answer->getSurveyQuestion()->getType() == 'radio' || $answer->getSurveyQuestion()->getType() == 'list')) || $answer->getSurveyQuestion()->getOptional()){
                        continue;
                    }
                    if(!isset($countAnswer[$answer->getSurveyQuestion()->getId()])){
                        $countAnswer[$answer->getSurveyQuestion()->getId()] = array();
                    }
                    if(!isset($countAnswer[$answer->getSurveyQuestion()->getId()][$answer->getAnswer()])){
                        $countAnswer[$answer->getSurveyQuestion()->getId()][$answer->getAnswer()] = 0;
                    }
                    $countAnswer[$answer->getSurveyQuestion()->getId()][$answer->getAnswer()]++;
                }
            }

            foreach($surveyTaken->getSurveyAnswers() as $answer){
                if( (!($answer->getSurveyQuestion()->getType() == 'radio' || $answer->getSurveyQuestion()->getType() == 'list')) || $answer->getSurveyQuestion()->getOptional()){
                    continue;
                }
                $answer->setAnswer(array_keys($countAnswer[$answer->getSurveyQuestion()->getId()], max($countAnswer[$answer->getSurveyQuestion()->getId()]))[0]);
            }


            $surveyTaken->setSchool($em->getRepository('AppBundle:SurveyTaken')->findBy(array('survey' => $survey), array('id' => 'DESC') , 1)[0]->getSchool());

            $form = $this->createForm(new SurveyExecuteType(), $surveyTaken);
        }


        return $this->render('survey/takeSurvey.html.twig', array(
            'form' => $form->createView(),

        ));
    }


    public function createSurveyAction(Request $request)
    {
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

    public function copySurveyAction(Request $request, Survey $survey){
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getEntityManager();

        $new_survey = new Survey();

        // Check if user submitted a form with a survey
        $form = $this->createForm(new SurveyType(), $new_survey);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($new_survey);
                $em->flush();

                return $this->redirect($this->generateUrl('surveys'));
            }else{
                return $this->render('survey/survey_create.html.twig', array('form' => $form->createView()));
            }
        }

        // Nothing was submitted, this is the first request. Make a new copy of the requested survey
        $new_survey = clone $survey;

        $new_survey->setSemester($em->getRepository('AppBundle:Semester')->findCurrentSemesterByDepartment($user->getFieldOfStudy()->getDepartment()));
        foreach($survey->getSurveyQuestions() as $q){
            $new_q = clone $q;
            foreach($q->getAlternatives() as $a){
                $new_a = clone $a;
                $new_q->addAlternative($new_a);
                $new_a->setSurveyQuestion($new_q);
            }
            $new_survey->addSurveyQuestion($new_q);
        }

        $form = $this->createForm(new SurveyType(), $new_survey);
        return $this->render('survey/survey_create.html.twig', array('form' => $form->createView()));
    }

    public function showSurveysAction()
    {
        $surveys = $this->getDoctrine()->getRepository('AppBundle:Survey')->findBy([], ['id' => 'DESC']);
        foreach($surveys as $survey){
            $sql = "
                  SELECT COUNT(id)
                  FROM survey_taken
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
     * Deletes the given Survey.
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

    public function resultSurveyAction(Survey $survey)
    {
        $textQuestionArray = array();
        $textQAarray = array();

        // Get all text questions
        foreach($survey->getSurveyQuestions() as $question){
            if($question->getType()=="text"){
                $textQuestionArray[] = $question;
            }
        }

        //Collect text answers
        foreach($textQuestionArray as $textQuestion){
            $questionText = $textQuestion->getQuestion();
            $textQAarray[$questionText] = array();
            foreach($textQuestion->getAnswers() as $answer){
                $textQAarray[$questionText][] = $answer->getAnswer();
            }
        }

        return $this->render('survey/survey_result.html.twig', array(
            'textAnswers' => $textQAarray,
            'survey' => $survey,
        ));

    }

    public function getSurveyResultAction(Survey $survey){
        return new JsonResponse(array('survey' => $survey, 'answers' => $this->getDoctrine()->getRepository('AppBundle:SurveyTaken')->findAllTakenBySurvey($survey)));
    }


}

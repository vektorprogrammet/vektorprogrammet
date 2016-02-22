<?php

namespace AppBundle\Controller;

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

    public function copySurvey(Request $request){
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException();
        }
    }

    public function showSurveysAction()
    {
        $surveys = $this->getDoctrine()->getRepository('AppBundle:Survey')->findAll();
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
        $surveyTakenList = $this->getDoctrine()->getRepository('AppBundle:SurveyTaken')->findBySurvey($survey);

        //ADD SCHOOL CHART
        $schoolCompletedCount = array();
        foreach($surveyTakenList as $surveyTaken){
            $school = $surveyTaken->getSchool()->getName();
            if(array_key_exists($school,$schoolCompletedCount)){
                $schoolCompletedCount[$school]++;
            }else {
                $schoolCompletedCount[$school] = 1;
            }
        }

        $chart = new DataTable();
        $chart->addColumn('1', 'Label', 'string');
        $chart->addColumn('2', 'Quantity', 'number');
        $alternativeArray = array_keys($schoolCompletedCount);
        foreach($alternativeArray as $alternative){
            $chart->addRow([$alternative, $schoolCompletedCount[$alternative]]);
        }
        $diagramArray[] = $chart->toArray();
        $questionArray[] = 'Skole';
        //SCHOOL CHART END

        $lablesArray = array();
        $questionsArray = array();
        $textQuestionArray = array();
        $textQAarray = array();
        $counter = 0;

        //Create question charts
        foreach($survey->getSurveyQuestions() as $question){
            if($question->getType()!="text"){
                array_push($lablesArray, $question->getQuestion());
                array_push($questionsArray, $question);
            }else{
                $textQuestionArray[] = $question;
            }
        }
        foreach($textQuestionArray as $textQuestion){
            $questionText = $textQuestion->getQuestion();
            $textQAarray[$questionText] = array();
            foreach($textQuestion->getAnswers() as $answer){
                $textQAarray[$questionText][] = $answer->getAnswer();
            }
        }

        foreach($questionsArray as $question){
            $chart = new DataTable();
            $chart->addColumn('1', 'Label', 'string');
            $chart->addColumn('2', 'Quantity', 'number');
            $alternativeArray = $question->getAlternatives();

            foreach($alternativeArray as $alternative){
                $alternative = $alternative->getAlternative();
                $num = 0;
                foreach($question->getAnswers() as $answer){
                    if($answer->getAnswer() == $alternative){
                        $num++;
                    }
                }
                $chart->addRow([$alternative, intval($num)]);
            }

            $diagramArray[] = $chart->toArray();
            $questionArray[] = $lablesArray[$counter];
            $counter++;

        }
        return $this->render('survey/survey_result.html.twig', array(
            'textAnswers' => $textQAarray,
            'numAnswered' => sizeof($surveyTakenList),
            'survey' => $survey,
            'diagram' => $diagramArray,
            'question' => $questionArray,
        ));

    }

}

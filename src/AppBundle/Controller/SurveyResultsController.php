<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SurveyResultsController extends Controller
{
    public function showAction()
    {
        return $this->render('survey/survey_results.html.twig');
    }

    public function renderPupilFilterButtonsAction()
    {
        return $this->render('survey/pupil_results_filter.html.twig');
    }

    public function renderTeacherFilterButtonsAction()
    {
        return $this->render('survey/teacher_results_filter.html.twig');
    }

    public function showTeacherAction($filter = null)
    {
        $filterArray = array();

        if ($filter != null) {
            array_push($filterArray, explode('.', $filter));
            $filterArray = $filterArray[0];
            $i = 0;

            foreach ($filterArray as $Afilter) {
                $filterArray[$i] = array_map('intval', explode(',', $Afilter));
                ++$i;
            }
        }

        //Number of questions in the survey
        $NUMBEROFQUESTIONS = 10;
        //Questions in the survey
        $resultQuestionsArray = [
                'Antall svar per skole',
                'Klassetrinn',
                'Det var nyttig å ha vektorassistentene i klassen.',
                'Vektorassistentene var kvalifiserte for jobben.',
                'Det var for mange studentassistenter tilstede.',
                'Det var god kontakt og informasjonsflyt på forhånd.',
                'Jeg ønsker at prosjektet fortsetter.',
                'Jeg tror elevene har blitt mer motivert for matematikk som følge av prosjektet.',
                'Arbeidsbelastningen ble mindre når vektorassistentene var på skolen.',
                'Undervisning ble bedre tilpasset for elevene.',
                'Har du noen kommentarer om vektorprogrammet som vi kan bruke videre?',
        ];

        //Connects with the SurveyTecher entity
        $repositorySurvey = $this->getDoctrine()->getRepository('AppBundle:SurveyTeacher');
        //Connects with the School entity
        $repositorySchool = $this->getDoctrine()->getRepository('AppBundle:School');

        //Finds numeber of surveys
        if ($filter != null) {
            $numberOfSurveys = $repositorySurvey->NumOfSurveysFiltered($filterArray);
        } else {
            //Finds number of survey answers for each school
            $numberOfSurveys = $repositorySurvey->NumOfSurveys();
        }
        //Finds the textual feedback
        $feedBack = $repositorySurvey->allFeedback();

        //Creates the DataTable with two columns
        $schoolChart = new DataTable();
        $schoolChart->addColumn('1', 'Alternantive', 'string');
        $schoolChart->addColumn('2', 'Quantity', 'number');

        //Loops through the schools
        foreach ($repositorySchool->findAll() as $school) {
            //Finds the names for the schools
            $schoolName = $repositorySchool->find($school)->getName();

            if ($filter != null) {
                $schoolNumber = $repositorySurvey->NumOfSchoolsByIdFiltered($school, $filterArray);
            } else {
                //Finds number of survey answers for each school
                $schoolNumber = $repositorySurvey->NumOfSchoolsBySchool($school);
            }
            //Adds the information in the DataTable
            $schoolChart->addRow([$schoolName, intval($schoolNumber)]);
        }

        //Creates two arrays that will be sent into the twig, with the information form the surveys, and the questions.
        $diagramArray[0] = $schoolChart->toArray();
        $questionArray[0] = $resultQuestionsArray[0];

        //Loop through the question
        for ($diagramCounter = 1; $diagramCounter < $NUMBEROFQUESTIONS; ++$diagramCounter) {
            //creates a new dataTable for the information from the surveys
            $chart = new DataTable();
            $chart->addColumn('1', 'Question', 'string');
            $chart->addColumn('2', 'Quantity', 'number');

            //Sets the correct alternatives for the respective questions
            if ($diagramCounter == 1) {
                $ALTERNATIVEARRAY = ['8. Klasse', '9. Klasse', '10. Klasse'];
                $NUMBEROFALTERNATIVES = 3;
            } else {
                $ALTERNATIVEARRAY = ['Helt enig', 'Noe enig', 'Nøytral', 'Noe uenig', 'Helt Uenig'];
                $NUMBEROFALTERNATIVES = 5;
            }

            for ($alternativeCounter = 1; $alternativeCounter < $NUMBEROFALTERNATIVES + 1; ++$alternativeCounter) {
                $alternative = $ALTERNATIVEARRAY[$alternativeCounter - 1];

                if ($filter != null) {
                    $answerNumber = $repositorySurvey->NumOfAnswersByQuestionIdAndAlternativesFiltered($diagramCounter, $alternativeCounter, $filterArray);
                } else {
                    $answerNumber = $repositorySurvey->NumOfAnswersByQuestionIdAndAlternatives($diagramCounter, $alternativeCounter);
                }
                $chart->addRow([$alternative, intval($answerNumber)]);
            }
            $diagramArray[$diagramCounter] = $chart->toArray();
            $questionArray[$diagramCounter] = $resultQuestionsArray[$diagramCounter - 1];
        }
        $questionArray[10] = $resultQuestionsArray[9];

        return $this->render('survey/survey_data.html.twig', array(
                                                                    'id' => 'teacher',
                                                                    'diagram' => $diagramArray,
                                                                    'question' => $questionArray,
                                                                    'numSurveys' => $numberOfSurveys,
                                                                    'feedback' => $feedBack,
                                                                    'info' => 'Lærerundersøkelsene', ));
    }
}

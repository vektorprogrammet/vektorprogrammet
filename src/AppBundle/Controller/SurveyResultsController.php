<?php

namespace AppBundle\Controller;

use SaadTazi\GChartBundle\DataTable\DataTable;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SurveyResultsController extends Controller
{
    public function getName()
    {
        return 'surveyResults'; // This must be unique
    }
    public function showAction()
    {
        //return $this->showPupilAction();
        //return $this->showTeacherAction();
        return $this->render('survey/survey_results.html.twig');
    }

    //public $filterArray = array();

    public function showPupilAction($filter = null)
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

        //Number of surveyquestions
        $NUMBEROFQUESTIONS = 9;
        //Questions in the survey
        $resultQuestionsArray = [
            'Antall svar per skole',
            'Kjønnsfordeling',
            'Klassefordeling',
            'Var tilstede',
            'Har fått hjelp av vektorassistentene',
            'Det gikk greit å spørre om hjelp',
            'Lettere å spørre om hjelp studentene i timen',
            'Studentene kunne pensum',
            'Mer spennende å jobbe med matte',
            'Jeg vil at assistentene kommer tilbake', ];

        //Connects with the SurveyPupil entity
        $repositorySurvey = $this->getDoctrine()->getRepository('AppBundle:SurveyPupil');
        //Connects with the School entity
        $repositorySchool = $this->getDoctrine()->getRepository('AppBundle:School');

        //find the number of schools in the database

        if ($filter != null) {
            $numberOfSurveys = $repositorySurvey->NumOfSurveysFiltered($filterArray);
        } else {
            //Finds number of survey answers for each school
            $numberOfSurveys = $repositorySurvey->NumOfSurveys();
        }

        //Creates a datatable for the google chart, with two columns, one with alternatives, and one with number of answers.
        $schoolChart = new DataTable();
        $schoolChart->addColumn('1', 'Alternative', 'string');
        $schoolChart->addColumn('2', 'Quantity', 'number');

        //Loops through the schools
        foreach ($repositorySchool->findAll() as $school) {
            //Finds the names for the schools
            $schoolName = $school->getName();

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

        //Loops through the questions
        for ($diagramCounter = 1; $diagramCounter < $NUMBEROFQUESTIONS + 1; ++$diagramCounter) {
            //Creates a DataTable to store the chartdata in, with alternative, and number of answers for that alternative
            $chart = new DataTable();
            $chart->addColumn('1', 'Alternatives', 'string');
            $chart->addColumn('2', 'Quantity', 'number');
            //Checks which diagram, or question we are at, and sets the correct alternativeArray for that question
            if ($diagramCounter == 1) {
                $ALTERNATIVEARRAY = ['Gutter', 'Jenter'];
                $NUMBEROFALTERNATIVES = 2;
            } elseif ($diagramCounter == 2) {
                $ALTERNATIVEARRAY = ['5. Klasse', '6. Klasse', '7. Klasse', '8. Klasse', '9. Klasse', '10. Klasse'];
                $NUMBEROFALTERNATIVES = 6;
            } elseif ($diagramCounter == 3 or $diagramCounter == 4) {
                $ALTERNATIVEARRAY = ['Ja', 'Nei'];
                $NUMBEROFALTERNATIVES = 2;
            } else {
                $ALTERNATIVEARRAY = ['Helt enig', 'Noe enig', 'Nøytral', 'Noe uenig', 'Helt Uenig'];
                $NUMBEROFALTERNATIVES = 5;
            }

            //Loops through all the alternatives for the question.
            for ($alternativeCounter = 1; $alternativeCounter < $NUMBEROFALTERNATIVES + 1; ++$alternativeCounter) {
                $alternative = $ALTERNATIVEARRAY[$alternativeCounter - 1];
                //finds number of answers for each alternative
                if ($filter != null) {
                    $answerNumber = $repositorySurvey->NumOfAnswersByQuestionIdAndAlternativesFiltered($diagramCounter, $alternativeCounter, $filterArray);
                } else {
                    $answerNumber = $repositorySurvey->NumOfAnswersByQuestionIdAndAlternatives($diagramCounter, $alternativeCounter);
                }
                //Adds the information in the datatable
                $chart->addRow([$alternative, intval($answerNumber)]);
            }
            //Adds the data to the arrays, so it can be sent to the twig file
            $diagramArray[$diagramCounter] = $chart->toArray();
            $questionArray[$diagramCounter] = $resultQuestionsArray[$diagramCounter - 1];
        }
        $questionArray[10] = $resultQuestionsArray[9];
        //Renders the survey_results twig, and sends inn the questionaray, and numbers for the alternatives.
        return $this->render('survey/survey_data.html.twig', array(
                                                            'id' => 'pupil',
                                                            'diagram' => $diagramArray,
                                                            'question' => $questionArray,
                                                            'numSurveys' => $numberOfSurveys,
                                                            'info' => 'Elevundersøkelsene',

        ));
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

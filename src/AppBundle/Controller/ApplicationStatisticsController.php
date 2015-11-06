<?php

namespace AppBundle\Controller;

use SaadTazi\GChartBundle\DataTable\DataTable;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ApplicationStatisticsController extends Controller
{

    public function getName()
    {
        return 'applicationResults'; // This must be unique
    }

    public function showAction()
    {

        $NUMBEROFCHARTS = 7;

        $lablesArray = [
            "Semester",
            "Kjønn",
            "Har vært vektorassistent tidligere",
            "Antall tatt opp",
            "Studieår",
            "Studie",
            "Avdeling"];

        $repositoryApplicationStatistic = $this->getDoctrine()->getRepository('AppBundle:ApplicationStatistic');
        $repositorySemester = $this->getDoctrine()->getRepository('AppBundle:Semester');
        $repositoryFieldOfStudy = $this->getDoctrine()->getRepository('AppBundle:FieldOfStudy');
        $repositoryDepartment = $this->getDoctrine()->getRepository('AppBundle:Department');

        $numberOfApplications = $repositoryApplicationStatistic->NumOfApplications();


        for ($diagramCounter = 0; $diagramCounter < $NUMBEROFCHARTS; $diagramCounter++) {
            $chart = new DataTable();
            $chart->addColumn('1', 'Label', 'string');
            $chart->addColumn('2', 'Quantity', 'number');
            $AlternativeArray = [];

            if ($diagramCounter == 0) {
                $semesterCounter = 0;
                foreach ($repositorySemester->findAllSemesters() as $semester) {
                    array_push($AlternativeArray, $semester->getName().' '.$semester->getDepartment()->getShortName());
                    $alternative = $AlternativeArray[$semesterCounter];
                    $semesterNumber = $repositoryApplicationStatistic->NumOfSemester($semester);

                    $chart->addRow([$alternative, intVal($semesterNumber)]);
                    $semesterCounter++;
                }

            } elseif ($diagramCounter == 1) {
                $AlternativeArray = ['Gutt', 'Jente'];
                for ($alternativeCounter = 0; $alternativeCounter < count($AlternativeArray); $alternativeCounter++) {
                    $alternative = $AlternativeArray[$alternativeCounter];
                    $genderNumber = $repositoryApplicationStatistic->numOfGender($alternativeCounter);

                    $chart->addRow([$alternative, intval($genderNumber)]);
                }
            } elseif ($diagramCounter == 2) {
                $AlternativeArray = ['Nei', 'Ja'];
                for ($alternativeCounter = 0; $alternativeCounter < count($AlternativeArray); $alternativeCounter++) {
                    $alternative = $AlternativeArray[$alternativeCounter];
                    $participationNumber = $repositoryApplicationStatistic->NumOfPreviousParticipation($alternativeCounter);

                    $chart->addRow([$alternative, intval($participationNumber)]);
                }

            } elseif ($diagramCounter == 3) {
                $AlternativeArray = ['Ikke tatt opp', 'Tatt opp'];
                for ($alternativeCounter = 0; $alternativeCounter < count($AlternativeArray); $alternativeCounter++) {
                    $alternative = $AlternativeArray[$alternativeCounter];
                    $acceptedNumber = $repositoryApplicationStatistic->NumOfAccepted($alternativeCounter);

                    $chart->addRow([$alternative, intval($acceptedNumber)]);
                }
            } elseif ($diagramCounter == 4) {

                $AlternativeArray = ['1', '2', '3', '4', '5'];
                for ($alternativeCounter = 0; $alternativeCounter < count($AlternativeArray); $alternativeCounter++) {
                    $alternative = "".$AlternativeArray[$alternativeCounter].". året";
                    $acceptedNumber = $repositoryApplicationStatistic->NumOfYearOfStudy($alternativeCounter + 1);

                    $chart->addRow([$alternative, intval($acceptedNumber)]);
                }
            } elseif ($diagramCounter == 5) {

                $semesterCounter = 0;
                foreach ($repositoryFieldOfStudy->findAllFieldOfStudy() as $fieldOfStudy) {
                    array_push($AlternativeArray, $fieldOfStudy->getName());
                    $alternative = $AlternativeArray[$semesterCounter];
                    $fieldOfStudyNumber = $repositoryApplicationStatistic->NumOfFieldOfStudy($fieldOfStudy);

                    $chart->addRow([$alternative, intVal($fieldOfStudyNumber)]);
                    $semesterCounter++;
                }

            } else {
                $semesterCounter = 0;
                foreach ($repositoryDepartment->findAllDepartments() as $department) {
                    array_push($AlternativeArray, $department->getShortName());
                    $alternative = $AlternativeArray[$semesterCounter];

                    $semesterNumber = $repositoryApplicationStatistic->NumOfDepartment($department->getId());

                    $chart->addRow([$alternative, intVal($semesterNumber)]);
                    $semesterCounter++;
                }

            }


            $diagramArray[$diagramCounter] = $chart->toArray();
            $questionArray[$diagramCounter] = $lablesArray[$diagramCounter];

        }


        return $this->render('statistics/statistics.html.twig', array(
            'diagram' => $diagramArray,
            'question' => $questionArray,
            //'numSurveys' => $numberOfSurveys,
        ));



    }
}
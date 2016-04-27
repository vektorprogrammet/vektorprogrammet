<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ApplicationStatisticsController extends Controller
{

    public function getName()
    {
        return 'applicationResults'; // This must be unique
    }

    /**
     * @param Department|null $department
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Department $department = null, Request $request)
    {
        $semesterId = $request->get('semester');

        // Set default department and semester
        if(is_null($department))$department = $this->getUser()->getFieldOfStudy()->getDepartment();
        if(is_null($semesterId)){
            $semester = $this->getDoctrine()->getRepository('AppBundle:Semester')->findLatestSemesterByDepartmentId($department->getId());
        }else{
            $semester = $this->getDoctrine()->getRepository('AppBundle:Semester')->find($semesterId);
        }

        // 404 exception if semester does not belong to department
        if($semester->getDepartment()->getId() != $department->getId())throw $this->createNotFoundException('Denne siden finnes ikke.');

        // Get all departments and semesters. Used for navigation
        $departments = $this->getDoctrine()->getRepository('AppBundle:Department')->findAll();
        $semesters = $this->getDoctrine()->getRepository('AppBundle:Semester')->findAllSemestersByDepartment($department);

        // Repositories
        $applicationRepository = $this->getDoctrine()->getRepository('AppBundle:Application');
        $assistantHistoryRepository = $this->getDoctrine()->getRepository('AppBundle:AssistantHistory');

        // Application data
        $applicationCount = $applicationRepository->NumOfApplications($semester);
        $maleCount = $applicationRepository->numOfGender($semester, 0);
        $femaleCount = $applicationRepository->numOfGender($semester, 1);
        $prevParticipationCount = $applicationRepository->numOfPreviousParticipation($semester);

        // Count fieldOfStudy and studyYear data
        $fieldOfStudyCount = array();
        $studyYearCount = array();
        $applicants = $applicationRepository->findBy(array('semester'=>$semester));
        foreach($applicants as $applicant){

            $fieldOfStudyShortName = $applicant->getUser()->getFieldOfStudy()->getShortName();
            if(array_key_exists($fieldOfStudyShortName, $fieldOfStudyCount)) {
                $fieldOfStudyCount[$fieldOfStudyShortName]++;
            } else {
                $fieldOfStudyCount[$fieldOfStudyShortName] = 1;
            }

            $studyYear = $applicant->getYearOfStudy();
            $studyYearCount[$studyYear] = array_key_exists($studyYear, $studyYearCount) ? $studyYearCount[$studyYear] + 1 : 1;
        }
        ksort($fieldOfStudyCount);
        ksort($studyYearCount);



        // Accepted Application Data
        $assistantHistories = $assistantHistoryRepository->findBy(array('semester' => $semester));
        $cancelledInterviewsCount = count($applicationRepository->findCancelledApplicants($semester));
        $acceptedFemaleCount = $assistantHistoryRepository->numFemale($semester);
        $acceptedMaleCount = $assistantHistoryRepository->numMale($semester);

        return $this->render('statistics/statistics.html.twig', array(
            'applicationCount' => $applicationCount,
            'maleCount' => $maleCount,
            'femaleCount' => $femaleCount,
            'prevParticipationCount' => $prevParticipationCount,
            'fieldOfStudyCount' => $fieldOfStudyCount,
            'studyYearCount' => $studyYearCount,
            'assistantHistoriesCount' => count($assistantHistories),
            'cancelledInterviewsCount' => $cancelledInterviewsCount,
            'acceptedMaleCount' => $acceptedMaleCount,
            'acceptedFemaleCount' => $acceptedFemaleCount,
            'departments' => $departments,
            'semesters' => $semesters,
            'department' => $department,
            'semester' => $semester
        ));



    }
}
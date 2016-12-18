<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ApplicationStatisticsController extends Controller
{
    /**
     * @param Department|null $department
     * @param Request         $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Department $department = null, Request $request)
    {
        $semesterId = $request->get('semester');

        // Set default department and semester
        if (is_null($department)) {
            $department = $this->getUser()->getFieldOfStudy()->getDepartment();
        }
        if (is_null($semesterId)) {
            $semester = $this->getDoctrine()->getRepository('AppBundle:Semester')->findLatestSemesterByDepartmentId($department->getId());
        } else {
            $semester = $this->getDoctrine()->getRepository('AppBundle:Semester')->find($semesterId);
        }

        // 404 exception if semester does not belong to department
        if ($semester->getDepartment()->getId() != $department->getId()) {
            throw $this->createNotFoundException('Denne siden finnes ikke.');
        }

        $applicationData = $this->get('application.data');
        $applicationData->setSemester($semester);
        $assistantHistoryData = $this->get('assistant_history.data');
        $assistantHistoryData->setSemester($semester);

        return $this->render('statistics/statistics.html.twig', array(
            'applicationData' => $applicationData,
            'assistantHistoryData' => $assistantHistoryData,
            'department' => $department,
            'semester' => $semester,
        ));
    }
}

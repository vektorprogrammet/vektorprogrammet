<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApplicationStatisticsController extends BaseController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction()
    {
        $department = $this->getDepartmentOrThrow404();
        $semester = $this->getSemesterOrThrow404();
        $admissionPeriod = $this->getDoctrine()
            ->getRepository('AppBundle:AdmissionPeriod')
            ->findOneByDepartmentAndSemester($department, $semester);

        $assistantHistoryData = $this->get('assistant_history.data');
        $assistantHistoryData->setSemester($semester)->setDepartment($department);

        $applicationData = $this->get('application.data');
        if ($admissionPeriod !== null) {
            $applicationData->setAdmissionPeriod($admissionPeriod);
        }

        return $this->render('statistics/statistics.html.twig', array(
            'applicationData' => $applicationData,
            'assistantHistoryData' => $assistantHistoryData,
            'semester' => $semester,
            'department' => $department,
        ));
    }
}

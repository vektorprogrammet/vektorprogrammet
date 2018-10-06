<?php

namespace AppBundle\Controller;

class ApplicationStatisticsController extends BaseController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction()
    {
        $department = $this->getDepartmentOrThrow404();
        $semester = $this->getSemesterOrThrow404();
        $assistantHistoryData = $this->get('assistant_history.data');
        $assistantHistoryData->setSemester($semester)->setDepartment($department);

        $admissionPeriod = $this->getDoctrine()->getRepository('AppBundle:AdmissionPeriod')
            ->findOneByDepartmentAndSemester($department, $semester);
        $applicationData = $this->get('application.data');
        $applicationData->setAdmissionPeriod($admissionPeriod);

        return $this->render('statistics/statistics.html.twig', array(
            'applicationData' => $applicationData,
            'assistantHistoryData' => $assistantHistoryData,
            'semester' => $semester,
            'department' => $department,
        ));
    }
}

<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Semester;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ApplicationStatisticsController extends Controller
{
    /**
     * @param Semester $semester
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Semester $semester = null)
    {
        if ($semester === null) {
            $department = $this->getUser()->getDepartment();
            $semester = $department->getCurrentOrLatestSemester();
        }

        $applicationData = $this->get('application.data');
        $applicationData->setSemester($semester);
        $assistantHistoryData = $this->get('assistant_history.data');
        $assistantHistoryData->setSemester($semester);

        return $this->render('statistics/statistics.html.twig', array(
            'applicationData' => $applicationData,
            'assistantHistoryData' => $assistantHistoryData,
            'semester' => $semester,
        ));
    }
}

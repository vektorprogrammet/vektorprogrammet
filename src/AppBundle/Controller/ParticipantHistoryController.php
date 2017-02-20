<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ParticipantHistoryController extends Controller
{
    public function showAction()
    {
        /** @var Department $department */
        $department = $this->getUser()->getDepartment();

        return $this->showBySemesterAction($department->getCurrentOrLatestSemester());
    }

    public function showBySemesterAction(Semester $semester)
    {
        $department = $semester->getDepartment();

        // Find all work histories by department
        $workHistories = $this->getDoctrine()->getRepository('AppBundle:WorkHistory')->findWorkHistoriesByDepartment($department);

        // Find all assistantHistories by department
        $assistantHistories = $this->getDoctrine()->getRepository('AppBundle:AssistantHistory')->findAssistantHistoriesByDepartment($department, $semester);

        return $this->render('participant_history/index.html.twig', array(
            'workHistories' => $workHistories,
            'assistantHistories' => $assistantHistories,
            'semester' => $semester,
        ));
    }
}

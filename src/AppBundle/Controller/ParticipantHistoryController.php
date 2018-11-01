<?php

namespace AppBundle\Controller;

use AppBundle\Role\Roles;

class ParticipantHistoryController extends BaseController
{
    public function showAction()
    {
        $department = $this->getDepartmentOrThrow404();
        $semester = $this->getSemesterOrThrow404();

        if (!$this->isGranted(Roles::TEAM_LEADER) && $department !== $this->getUser()->getDepartment()) {
            throw $this->createAccessDeniedException();
        }

        // Find all team memberships by department
        $teamMemberships = $this->getDoctrine()->getRepository('AppBundle:TeamMembership')->findTeamMembershipsByDepartment($department);

        // Find all assistantHistories by department
        $assistantHistories = $this->getDoctrine()->getRepository('AppBundle:AssistantHistory')->findByDepartmentAndSemester($department, $semester);

        return $this->render('participant_history/index.html.twig', array(
            'teamMemberships' => $teamMemberships,
            'assistantHistories' => $assistantHistories,
            'semester' => $semester,
            'department' => $department,
        ));
    }
}

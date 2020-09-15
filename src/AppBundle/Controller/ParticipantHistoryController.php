<?php

namespace AppBundle\Controller;

use AppBundle\Role\Roles;

class ParticipantHistoryController extends BaseController
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function showAction(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);

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

<?php

namespace App\Controller;

use App\Entity\AssistantHistory;
use App\Entity\Repository\AssistantHistoryRepository;
use App\Entity\Repository\DepartmentRepository;
use App\Entity\Repository\SemesterRepository;
use App\Entity\Repository\TeamMembershipRepository;
use App\Entity\TeamMembership;
use App\Role\Roles;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ParticipantHistoryController extends BaseController
{
    public function __construct(
        private TeamMembershipRepository $teamMembershipRepo,
        private AssistantHistoryRepository $assistantHistoryRepo,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    /**
     * @param Request $request
     * @return Response|null
     */
    #[Route('/kontrollpanel/deltakerhistorikk', name: 'participanthistory_show', methods: ['GET'])]
    public function showAction(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);

        if (!$this->isGranted(Roles::TEAM_LEADER) && $department !== $this->getUser()->getDepartment()) {
            throw $this->createAccessDeniedException();
        }

        // Find all team memberships by department
        $teamMemberships = $this->teamMembershipRepo->findTeamMembershipsByDepartment($department);

        // Find all assistantHistories by department
        $assistantHistories = $this->assistantHistoryRepo->findByDepartmentAndSemester($department, $semester);

        return $this->render('participant_history/index.html.twig', array(
            'teamMemberships' => $teamMemberships,
            'assistantHistories' => $assistantHistories,
            'semester' => $semester,
            'department' => $department,
        ));
    }
}

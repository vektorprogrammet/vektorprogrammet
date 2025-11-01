<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AssistantHistory;
use AppBundle\Entity\TeamMembership;
use AppBundle\Repository\Contract\AssistantHistoryRepositoryInterface;
use AppBundle\Role\Roles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ParticipantHistoryController extends BaseController
{
    private $assistantHistoryRepository;
    private $entityManager;

    /**
     * @param AssistantHistoryRepositoryInterface $assistantHistoryRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        AssistantHistoryRepositoryInterface $assistantHistoryRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->assistantHistoryRepository = $assistantHistoryRepository;
        $this->entityManager = $entityManager;
    }
    /**
     * @param Request $request
     * @return Response|null
     */
    public function showAction(Request $request)
    {
        $department = $this->getDepartmentOrThrow404($request);
        $semester = $this->getSemesterOrThrow404($request);

        if (!$this->isGranted(Roles::TEAM_LEADER) && $department !== $this->getUser()->getDepartment()) {
            throw $this->createAccessDeniedException();
        }

        // Find all team memberships by department
        $teamMemberships = $this->entityManager->getRepository(TeamMembership::class)->findTeamMembershipsByDepartment($department);

        // Find all assistantHistories by department
        $assistantHistories = $this->assistantHistoryRepository->findByDepartmentAndSemester($department, $semester);

        return $this->render('participant_history/index.html.twig', array(
            'teamMemberships' => $teamMemberships,
            'assistantHistories' => $assistantHistories,
            'semester' => $semester,
            'department' => $department,
        ));
    }
}

<?php

namespace App\Service;

use App\Models\Department;
use App\Models\Team;
use App\Models\TeamMembership;
use App\Models\User;
use App\Repository\Contract\TeamRepositoryInterface;
use App\Service\Contract\TeamAdminServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service for team admin operations.
 * Extracts business logic from TeamAdminController.
 */
class TeamAdminService implements TeamAdminServiceInterface
{
    private $teamRepository;
    private $entityManager;

    /**
     * @param TeamRepositoryInterface $teamRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        TeamRepositoryInterface $teamRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->teamRepository = $teamRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getTeamsData(Department $department): array
    {
        $activeTeams = $this->teamRepository->findActiveByDepartment($department);
        $inactiveTeams = $this->teamRepository->findInactiveByDepartment($department);

        return [
            'active_teams' => $activeTeams,
            'inactive_teams' => $inactiveTeams,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecificTeamData(Team $team, User $currentUser): array
    {
        $teamMembershipRepo = $this->entityManager->getRepository(TeamMembership::class);
        $activeTeamMemberships = $teamMembershipRepo->findActiveTeamMembershipsByTeam($team);
        $inActiveTeamMemberships = $teamMembershipRepo->findInactiveTeamMembershipsByTeam($team);

        $activeTeamMemberships = $this->sortTeamMembershipsByStartDate($activeTeamMemberships);
        $inActiveTeamMemberships = $this->sortTeamMembershipsByStartDate($inActiveTeamMemberships);

        $teamMembershipRepo = $this->entityManager->getRepository(TeamMembership::class);
        $currentUserTeamMembership = $teamMembershipRepo->findActiveTeamMembershipsByUser($currentUser);
        $isUserInTeam = false;
        foreach ($currentUserTeamMembership as $membership) {
            if (in_array($membership, $activeTeamMemberships)) {
                $isUserInTeam = true;
                break;
            }
        }

        return [
            'team' => $team,
            'activeTeamMemberships' => $activeTeamMemberships,
            'inActiveTeamMemberships' => $inActiveTeamMemberships,
            'isUserInTeam' => $isUserInTeam,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function sortTeamMembershipsByStartDate(array $teamMemberships): array
    {
        usort($teamMemberships, function (TeamMembership $a, TeamMembership $b) {
            return $a->getStartSemester()->getStartDate() < $b->getStartSemester()->getStartDate() ? -1 : 1;
        });

        return $teamMemberships;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteTeam(Team $team): void
    {
        foreach ($team->getTeamMemberships() as $teamMembership) {
            $teamMembership->setDeletedTeamName($team->getName());
            $this->entityManager->persist($teamMembership);
        }

        $this->entityManager->remove($team);
        $this->entityManager->flush();
    }
}


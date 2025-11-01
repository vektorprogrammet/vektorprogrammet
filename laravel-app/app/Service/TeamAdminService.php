<?php

namespace App\Service;

use App\Models\Department;
use App\Models\Team;
use App\Models\TeamMembership;
use App\Models\User;
use App\Repository\Contract\TeamMembershipRepositoryInterface;
use App\Repository\Contract\TeamRepositoryInterface;
use App\Service\Contract\TeamAdminServiceInterface;

/**
 * Service for team admin operations.
 * Extracts business logic from TeamAdminController.
 */
class TeamAdminService implements TeamAdminServiceInterface
{
    private TeamRepositoryInterface $teamRepository;
    private TeamMembershipRepositoryInterface $teamMembershipRepository;

    /**
     * @param TeamRepositoryInterface $teamRepository
     * @param TeamMembershipRepositoryInterface $teamMembershipRepository
     */
    public function __construct(
        TeamRepositoryInterface $teamRepository,
        TeamMembershipRepositoryInterface $teamMembershipRepository
    ) {
        $this->teamRepository = $teamRepository;
        $this->teamMembershipRepository = $teamMembershipRepository;
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
        $activeTeamMemberships = $this->teamMembershipRepository->findActiveTeamMembershipsByTeam($team);
        $inActiveTeamMemberships = $this->teamMembershipRepository->findInactiveTeamMembershipsByTeam($team);

        $activeTeamMemberships = $this->sortTeamMembershipsByStartDate($activeTeamMemberships);
        $inActiveTeamMemberships = $this->sortTeamMembershipsByStartDate($inActiveTeamMemberships);

        $currentUserTeamMembership = $this->teamMembershipRepository->findActiveTeamMembershipsByUser($currentUser);
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
            return $a->startSemester->start_date < $b->startSemester->start_date ? -1 : 1;
        });

        return $teamMemberships;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteTeam(Team $team): void
    {
        foreach ($team->teamMemberships as $teamMembership) {
            $teamMembership->deleted_team_name = $team->name;
            $teamMembership->save();
        }

        $team->delete();
    }
}


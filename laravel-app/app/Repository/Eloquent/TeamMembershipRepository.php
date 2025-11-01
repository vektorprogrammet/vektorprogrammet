<?php

namespace App\Repository\Eloquent;

use App\Models\Department;
use App\Models\Semester;
use App\Models\Team;
use App\Models\TeamMembership;
use App\Models\User;
use App\Repository\Contract\TeamMembershipRepositoryInterface;
use Carbon\Carbon;

/**
 * Eloquent implementation of TeamMembershipRepositoryInterface.
 */
class TeamMembershipRepository implements TeamMembershipRepositoryInterface
{
    /**
     * Find team memberships by team.
     *
     * @param Team $team
     * @return TeamMembership[]
     */
    public function findByTeam(Team $team): array
    {
        return TeamMembership::where('team_id', $team->id)
            ->get()
            ->toArray();
    }

    /**
     * Find team memberships by user.
     *
     * @param User $user
     * @return TeamMembership[]
     */
    public function findByUser(User $user): array
    {
        return TeamMembership::where('user_id', $user->id)
            ->get()
            ->toArray();
    }

    /**
     * Find all active team memberships.
     *
     * @return TeamMembership[]
     */
    public function findActiveTeamMemberships(): array
    {
        $today = Carbon::now();
        
        return TeamMembership::query()
            ->whereHas('startSemester', function ($query) use ($today) {
                $query->where('start_date', '<', $today);
            })
            ->where(function ($query) use ($today) {
                $query->whereNull('end_semester_id')
                      ->orWhereHas('endSemester', function ($q) use ($today) {
                          $q->where('end_date', '>', $today);
                      });
            })
            ->get()
            ->filter(function ($membership) use ($today) {
                $currentSemester = $this->getCurrentSemester();
                return $this->isMembershipActiveInSemester($membership, $currentSemester);
            })
            ->values()
            ->toArray();
    }

    /**
     * Find active team memberships by team.
     *
     * @param Team $team
     * @return TeamMembership[]
     */
    public function findActiveTeamMembershipsByTeam(Team $team): array
    {
        $memberships = $this->findByTeam($team);
        $currentSemester = $this->getCurrentSemester();
        
        return $this->filterNotInSemester($memberships, $currentSemester);
    }

    /**
     * Filter team memberships to exclude those not in the given semester.
     *
     * @param TeamMembership[] $teamMemberships
     * @param Semester $semester
     * @return TeamMembership[]
     */
    public function filterNotInSemester(array $teamMemberships, Semester $semester): array
    {
        return array_filter($teamMemberships, function (TeamMembership $membership) use ($semester) {
            $startSemester = $membership->startSemester;
            $endSemester = $membership->endSemester;
            
            return $semester->isBetween($startSemester, $endSemester);
        });
    }

    /**
     * Find active team memberships by team and user.
     *
     * @param Team $team
     * @param User $user
     * @return TeamMembership[]
     */
    public function findActiveTeamMembershipsByTeamAndUser(Team $team, User $user): array
    {
        $memberships = TeamMembership::where('team_id', $team->id)
            ->where('user_id', $user->id)
            ->get()
            ->toArray();
        
        $currentSemester = $this->getCurrentSemester();
        return $this->filterNotInSemester($memberships, $currentSemester);
    }

    /**
     * Find inactive team memberships by team.
     *
     * @param Team $team
     * @return TeamMembership[]
     */
    public function findInactiveTeamMembershipsByTeam(Team $team): array
    {
        $allMemberships = $this->findByTeam($team);
        $activeMemberships = $this->findActiveTeamMembershipsByTeam($team);
        
        // Get IDs of active memberships
        $activeIds = array_map(function ($membership) {
            return is_object($membership) ? $membership->id : $membership['id'];
        }, $activeMemberships);
        
        // Filter out active ones
        return array_filter($allMemberships, function ($membership) use ($activeIds) {
            $id = is_object($membership) ? $membership->id : $membership['id'];
            return !in_array($id, $activeIds);
        });
    }

    /**
     * Find active team memberships by user.
     *
     * @param User $user
     * @return TeamMembership[]
     */
    public function findActiveTeamMembershipsByUser(User $user): array
    {
        $memberships = $this->findByUser($user);
        $currentSemester = $this->getCurrentSemester();
        
        return $this->filterNotInSemester($memberships, $currentSemester);
    }

    /**
     * Find team memberships by user and semester.
     *
     * @param User $user
     * @param Semester $semester
     * @return TeamMembership[]
     */
    public function findTeamMembershipsByUserAndSemester($user, Semester $semester): array
    {
        $memberships = $this->findByUser($user);
        return $this->filterNotInSemester($memberships, $semester);
    }

    /**
     * Find team memberships by department.
     *
     * @param Department $department
     * @return TeamMembership[]
     */
    public function findTeamMembershipsByDepartment(Department $department): array
    {
        return TeamMembership::query()
            ->whereHas('team', function ($query) use ($department) {
                $query->where('department_id', $department->id);
            })
            ->get()
            ->toArray();
    }

    /**
     * Get current semester based on today's date.
     *
     * @return Semester|null
     */
    private function getCurrentSemester(): ?Semester
    {
        $now = Carbon::now();
        $year = (string) $now->year;
        $month = $now->month;
        $semesterTime = ($month >= 1 && $month <= 7) ? 'Vår' : 'Høst';

        return Semester::where('year', $year)
            ->where('semester_time', $semesterTime)
            ->first();
    }

    /**
     * Check if membership is active in the given semester.
     *
     * @param TeamMembership $membership
     * @param Semester|null $semester
     * @return bool
     */
    private function isMembershipActiveInSemester(TeamMembership $membership, ?Semester $semester): bool
    {
        if (!$semester) {
            return false;
        }

        $startSemester = $membership->startSemester;
        $endSemester = $membership->endSemester;

        return $semester->isBetween($startSemester, $endSemester);
    }
}


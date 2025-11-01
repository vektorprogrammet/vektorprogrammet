<?php

namespace App\Repository\Eloquent;

use App\Models\ExecutiveBoardMembership;
use App\Models\User;
use App\Repository\Contract\ExecutiveBoardMembershipRepositoryInterface;

/**
 * Eloquent implementation of ExecutiveBoardMembershipRepositoryInterface.
 */
class ExecutiveBoardMembershipRepository implements ExecutiveBoardMembershipRepositoryInterface
{
    /**
     * Find all executive board memberships.
     *
     * @return ExecutiveBoardMembership[]
     */
    public function findAll(): array
    {
        return ExecutiveBoardMembership::all()->toArray();
    }

    /**
     * Find executive board memberships by user.
     *
     * @param User $user
     * @return ExecutiveBoardMembership[]
     */
    public function findByUser(User $user): array
    {
        return ExecutiveBoardMembership::query()
            ->where('user_id', $user->id)
            ->join('semester', 'executive_board_membership.start_semester_id', '=', 'semester.id')
            ->orderByRaw("CASE WHEN semester.semester_time = 'Vår' THEN 1 WHEN semester.semester_time = 'Høst' THEN 2 END")
            ->orderBy('semester.year', 'desc')
            ->select('executive_board_membership.*')
            ->get()
            ->toArray();
    }
}


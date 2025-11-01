<?php

namespace App\Contract;

use App\Models\ExecutiveBoardMembership;

/**
 * Interface for executive board member management logic.
 */
interface ExecutiveBoardServiceInterface
{
    /**
     * Separate members into active and inactive arrays.
     *
     * @param ExecutiveBoardMembership[] $members All board members
     * @return array Array with 'active' and 'inactive' keys containing filtered members
     */
    public function separateMembersByStatus(array $members): array;
}


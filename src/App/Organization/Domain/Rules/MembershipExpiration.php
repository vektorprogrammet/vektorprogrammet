<?php

namespace App\Organization\Domain\Rules;

use App\Organization\Infrastructure\Entity\TeamMembership;

class MembershipExpiration
{
    /**
     * Check if a team membership has expired based on current semester start date.
     */
    public function isExpired(TeamMembership $membership, \DateTimeInterface $currentSemesterStartDate): bool
    {
        $endSemester = $membership->getEndSemester();
        if ($endSemester === null) {
            return false;
        }

        return $endSemester->getEndDate() <= $currentSemesterStartDate;
    }
}

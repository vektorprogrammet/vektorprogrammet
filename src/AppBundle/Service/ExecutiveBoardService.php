<?php

namespace AppBundle\Service;

use AppBundle\Entity\ExecutiveBoardMembership;
use AppBundle\Service\Contract\ExecutiveBoardServiceInterface;

/**
 * Service for executive board member management logic.
 */
class ExecutiveBoardService implements ExecutiveBoardServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function separateMembersByStatus(array $members): array
    {
        $activeMembers = [];
        $inactiveMembers = [];
        
        foreach ($members as $member) {
            if ($member->isActive()) {
                $activeMembers[] = $member;
            } else {
                $inactiveMembers[] = $member;
            }
        }

        return [
            'active' => $activeMembers,
            'inactive' => $inactiveMembers,
        ];
    }
}


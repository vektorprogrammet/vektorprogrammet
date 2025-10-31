<?php

namespace AppBundle\Service\Contract;

use AppBundle\Entity\User;

/**
 * Interface for Partner service operations.
 * This interface defines the contract for partner finding logic.
 */
interface PartnerServiceInterface
{
    /**
     * Find partner informations for a user based on their active assistant histories.
     *
     * @param User $user
     * @return array Partner informations with keys:
     *   - partnerInformations: array[] Array of partner info with 'school', 'assistantHistory', 'partners'
     *   - partnerCount: int Total number of partners found
     */
    public function findPartnersForUser(User $user): array;
}


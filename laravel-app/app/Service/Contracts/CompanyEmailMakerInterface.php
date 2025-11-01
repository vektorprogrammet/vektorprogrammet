<?php

namespace App\Contract;

use App\Models\User;

/**
 * Interface for CompanyEmailMaker service.
 * Defines contract for company email creation operations.
 */
interface CompanyEmailMakerInterface
{
    /**
     * Set company email for user.
     *
     * @param User $user
     * @param array $blackList
     * @return string|null
     */
    public function setCompanyEmailFor(User $user, array $blackList): ?string;
}

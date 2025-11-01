<?php

namespace AppBundle\Repository\Contract;

use AppBundle\Entity\StaticContent;

/**
 * Interface for StaticContent repository operations.
 * This interface defines the contract for static content data access,
 * making it easier to migrate to Laravel's repository pattern or Eloquent.
 */
interface StaticContentRepositoryInterface
{
    /**
     * Find static content by HTML ID.
     *
     * @param string $htmlId
     * @return StaticContent|null
     */
    public function findOneByHtmlId(string $htmlId): ?StaticContent;
}


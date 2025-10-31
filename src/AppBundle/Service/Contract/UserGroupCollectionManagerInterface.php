<?php

namespace AppBundle\Service\Contract;

use AppBundle\Entity\UserGroupCollection;

/**
 * Interface for UserGroupCollectionManager service.
 * Defines contract for user group collection operations.
 */
interface UserGroupCollectionManagerInterface
{
    /**
     * Initialize user group collection.
     *
     * @param UserGroupCollection $userGroupCollection
     */
    public function initializeUserGroupCollection(UserGroupCollection $userGroupCollection);
}

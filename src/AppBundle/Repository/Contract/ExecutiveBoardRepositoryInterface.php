<?php

namespace AppBundle\Repository\Contract;

use AppBundle\Entity\ExecutiveBoard;

/**
 * Interface for ExecutiveBoard repository operations.
 * This interface defines the contract for executive board data access,
 * making it easier to migrate to Laravel's repository pattern or Eloquent.
 */
interface ExecutiveBoardRepositoryInterface
{
    /**
     * Find the executive board.
     *
     * @return ExecutiveBoard
     */
    public function findBoard(): ExecutiveBoard;
}


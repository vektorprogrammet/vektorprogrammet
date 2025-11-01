<?php

namespace App\Repository\Eloquent;

use App\Models\ExecutiveBoard;
use App\Repository\Contract\ExecutiveBoardRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Eloquent implementation of ExecutiveBoardRepositoryInterface.
 */
class ExecutiveBoardRepository implements ExecutiveBoardRepositoryInterface
{
    /**
     * Find the executive board.
     *
     * @return ExecutiveBoard
     * @throws ModelNotFoundException
     */
    public function findBoard(): ExecutiveBoard
    {
        return ExecutiveBoard::firstOrFail();
    }
}


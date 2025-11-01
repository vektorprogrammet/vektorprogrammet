<?php

namespace App\Contract;

use App\Models\ExecutiveBoard;
use App\Models\ExecutiveBoardMembership;
use App\Models\User;

/**
 * Interface for ExecutiveBoardManagementService.
 *
 * Handles business logic for executive board membership management operations.
 */
interface ExecutiveBoardManagementServiceInterface
{
    /**
     * Create an executive board membership.
     *
     * @param ExecutiveBoardMembership $membership
     * @param ExecutiveBoard $board
     * @param User $user
     *
     * @return void
     */
    public function createMembership(ExecutiveBoardMembership $membership, ExecutiveBoard $board, User $user): void;

    /**
     * Update an executive board membership.
     *
     * @param ExecutiveBoardMembership $membership
     *
     * @return void
     */
    public function updateMembership(ExecutiveBoardMembership $membership): void;

    /**
     * Remove an executive board membership.
     *
     * @param ExecutiveBoardMembership $membership
     *
     * @return void
     */
    public function removeMembership(ExecutiveBoardMembership $membership): void;

    /**
     * Update the executive board.
     *
     * @param ExecutiveBoard $board
     *
     * @return void
     */
    public function updateBoard(ExecutiveBoard $board): void;
}


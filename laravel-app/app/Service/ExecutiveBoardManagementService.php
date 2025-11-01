<?php

namespace App\Service;

use App\Models\ExecutiveBoard;
use App\Models\ExecutiveBoardMembership;
use App\Models\User;
use App\Service\Contract\ExecutiveBoardManagementServiceInterface;
use App\Service\Contract\RoleManagerInterface;

/**
 * Service for managing executive board memberships and board-related business logic.
 */
class ExecutiveBoardManagementService implements ExecutiveBoardManagementServiceInterface
{
    private RoleManagerInterface $roleManager;

    /**
     * @param RoleManagerInterface $roleManager
     */
    public function __construct(
        RoleManagerInterface $roleManager
    ) {
        $this->roleManager = $roleManager;
    }

    /**
     * {@inheritdoc}
     */
    public function createMembership(ExecutiveBoardMembership $membership, ExecutiveBoard $board, User $user): void
    {
        $membership->board_id = $board->id;
        $membership->save();
        $this->roleManager->updateUserRole($membership->user);
    }

    /**
     * {@inheritdoc}
     */
    public function updateMembership(ExecutiveBoardMembership $membership): void
    {
        $membership->save();
    }

    /**
     * {@inheritdoc}
     */
    public function removeMembership(ExecutiveBoardMembership $membership): void
    {
        $user = $membership->user;
        $membership->delete();
        $this->roleManager->updateUserRole($user);
    }

    /**
     * {@inheritdoc}
     */
    public function updateBoard(ExecutiveBoard $board): void
    {
        $board->save();
    }
}


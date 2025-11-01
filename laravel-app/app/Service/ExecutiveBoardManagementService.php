<?php

namespace App\Service;

use App\Models\ExecutiveBoard;
use App\Models\ExecutiveBoardMembership;
use App\Models\User;
use App\Service\Contract\ExecutiveBoardManagementServiceInterface;
use App\Service\Contract\RoleManagerInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service for managing executive board memberships and board-related business logic.
 */
class ExecutiveBoardManagementService implements ExecutiveBoardManagementServiceInterface
{
    private $entityManager;
    private $roleManager;

    /**
     * @param EntityManagerInterface $entityManager
     * @param RoleManagerInterface $roleManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        RoleManagerInterface $roleManager
    ) {
        $this->entityManager = $entityManager;
        $this->roleManager = $roleManager;
    }

    /**
     * {@inheritdoc}
     */
    public function createMembership(ExecutiveBoardMembership $membership, ExecutiveBoard $board, User $user): void
    {
        $membership->setBoard($board);
        $this->entityManager->persist($membership);
        $this->entityManager->flush();
        $this->roleManager->updateUserRole($membership->getUser());
    }

    /**
     * {@inheritdoc}
     */
    public function updateMembership(ExecutiveBoardMembership $membership): void
    {
        $this->entityManager->persist($membership);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function removeMembership(ExecutiveBoardMembership $membership): void
    {
        $user = $membership->getUser();
        $this->entityManager->remove($membership);
        $this->entityManager->flush();
        $this->roleManager->updateUserRole($user);
    }

    /**
     * {@inheritdoc}
     */
    public function updateBoard(ExecutiveBoard $board): void
    {
        $this->entityManager->persist($board);
        $this->entityManager->flush();
    }
}


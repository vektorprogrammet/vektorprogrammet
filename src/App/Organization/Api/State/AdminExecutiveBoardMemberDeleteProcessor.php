<?php

declare(strict_types=1);

namespace App\Organization\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Organization\Infrastructure\Entity\ExecutiveBoardMembership;
use App\Identity\Infrastructure\RoleManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class AdminExecutiveBoardMemberDeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly RoleManager $roleManager,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $id = $uriVariables['id'] ?? null;
        $membership = $this->em->getRepository(ExecutiveBoardMembership::class)->find($id);

        if ($membership === null) {
            return;
        }

        $user = $membership->getUser();

        $this->em->remove($membership);
        $this->em->flush();

        try {
            $this->roleManager->updateUserRole($user);
        } catch (\Throwable $e) {
            $this->logger->error('RoleManager::updateUserRole failed after board member remove: '.$e->getMessage());
        }
    }
}

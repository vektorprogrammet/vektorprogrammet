<?php

declare(strict_types=1);

namespace App\Organization\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Organization\Infrastructure\Entity\TeamMembership;
use App\Organization\Domain\Events\TeamMembershipEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AdminTeamMembershipDeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $id = $uriVariables['id'] ?? null;
        $membership = $this->em->getRepository(TeamMembership::class)->find($id);

        if ($membership === null) {
            return;
        }

        $this->em->remove($membership);
        $this->em->flush();

        try {
            $this->eventDispatcher->dispatch(new TeamMembershipEvent($membership), TeamMembershipEvent::DELETED);
        } catch (\Throwable $e) {
            $this->logger->error('TeamMembershipEvent::DELETED dispatch failed: '.$e->getMessage());
        }
    }
}

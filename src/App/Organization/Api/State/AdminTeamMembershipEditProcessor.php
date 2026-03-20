<?php

namespace App\Organization\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Organization\Infrastructure\Entity\Position;
use App\Shared\Entity\Semester;
use App\Organization\Infrastructure\Entity\TeamMembership;
use App\Organization\Domain\Events\TeamMembershipEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminTeamMembershipEditProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $id = $uriVariables['id'] ?? null;
        $membership = $this->em->getRepository(TeamMembership::class)->find($id);

        if ($membership === null) {
            throw new NotFoundHttpException('Team membership not found.');
        }

        if ($data->positionId !== null) {
            $position = $this->em->getRepository(Position::class)->find($data->positionId);
            if ($position !== null) {
                $membership->setPosition($position);
            }
        }

        if ($data->startSemesterId !== null) {
            $startSemester = $this->em->getRepository(Semester::class)->find($data->startSemesterId);
            if ($startSemester !== null) {
                $membership->setStartSemester($startSemester);
            }
        }

        if ($data->endSemesterId !== null) {
            $endSemester = $this->em->getRepository(Semester::class)->find($data->endSemesterId);
            $membership->setEndSemester($endSemester);
        }

        $membership->setIsSuspended(false);

        $this->em->persist($membership);
        $this->em->flush();

        try {
            $this->eventDispatcher->dispatch(new TeamMembershipEvent($membership), TeamMembershipEvent::EDITED);
        } catch (\Throwable $e) {
            $this->logger->error('TeamMembershipEvent::EDITED dispatch failed: '.$e->getMessage());
        }

        return ['id' => $membership->getId()];
    }
}

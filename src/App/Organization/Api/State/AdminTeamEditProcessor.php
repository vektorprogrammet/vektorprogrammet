<?php

declare(strict_types=1);

namespace App\Organization\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Organization\Infrastructure\Entity\Team;
use App\Organization\Domain\Events\TeamEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminTeamEditProcessor implements ProcessorInterface
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
        $team = $this->em->getRepository(Team::class)->find($id);

        if ($team === null) {
            throw new NotFoundHttpException('Team not found.');
        }

        $oldTeamEmail = $team->getEmail();

        $team->setName($data->name);
        if ($data->email !== null) {
            $team->setEmail($data->email);
        }
        if ($data->shortDescription !== null) {
            $team->setShortDescription($data->shortDescription);
        }
        if ($data->description !== null) {
            $team->setDescription($data->description);
        }
        if ($data->acceptApplication !== null) {
            $team->setAcceptApplication($data->acceptApplication);
        }
        if ($data->active !== null) {
            $team->setActive($data->active);
        }
        if ($data->deadline !== null) {
            try {
                $team->setDeadline(new \DateTime($data->deadline));
            } catch (\Exception) {
                // ignore invalid date format
            }
        }

        $this->em->persist($team);
        $this->em->flush();

        try {
            $this->eventDispatcher->dispatch(new TeamEvent($team, $oldTeamEmail), TeamEvent::EDITED);
        } catch (\Throwable $e) {
            $this->logger->error('TeamEvent::EDITED dispatch failed: '.$e->getMessage());
        }

        return ['id' => $team->getId(), 'name' => $team->getName()];
    }
}

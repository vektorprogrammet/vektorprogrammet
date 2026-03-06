<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Department;
use App\Entity\Team;
use App\Event\TeamEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class AdminTeamCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $department = $data->departmentId
            ? $this->em->getRepository(Department::class)->find($data->departmentId)
            : null;

        if ($department === null) {
            throw new UnprocessableEntityHttpException('Invalid departmentId.');
        }

        $team = new Team();
        $team->setName($data->name);
        $team->setDepartment($department);

        if ($data->email !== null) {
            $team->setEmail($data->email);
        }
        if ($data->shortDescription !== null) {
            $team->setShortDescription($data->shortDescription);
        }
        if ($data->description !== null) {
            $team->setDescription($data->description);
        }

        $this->em->persist($team);
        $this->em->flush();

        try {
            $this->eventDispatcher->dispatch(new TeamEvent($team, $team->getEmail()), TeamEvent::CREATED);
        } catch (\Throwable $e) {
            $this->logger->error('TeamEvent::CREATED dispatch failed: '.$e->getMessage());
        }

        return ['id' => $team->getId(), 'name' => $team->getName()];
    }
}

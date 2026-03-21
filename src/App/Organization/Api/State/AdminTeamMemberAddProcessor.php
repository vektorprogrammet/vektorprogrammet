<?php

namespace App\Organization\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Identity\Infrastructure\AccessControlService;
use App\Organization\Infrastructure\Entity\Position;
use App\Shared\Entity\Semester;
use App\Organization\Infrastructure\Entity\Team;
use App\Organization\Infrastructure\Entity\TeamMembership;
use App\Identity\Infrastructure\Entity\User;
use App\Organization\Domain\Events\TeamMembershipEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class AdminTeamMemberAddProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly AccessControlService $accessControl,
        private readonly Security $security,
        private readonly EntityManagerInterface $em,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $teamId = $uriVariables['id'] ?? null;
        $team = $teamId ? $this->em->getRepository(Team::class)->find($teamId) : null;

        if ($team === null) {
            throw new NotFoundHttpException('Team not found.');
        }

        $department = $team->getDepartment();
        if ($department !== null) {
            /** @var User $user */
            $user = $this->security->getUser();
            $this->accessControl->assertDepartmentAccess($department, $user);
        }

        $user = $data->userId
            ? $this->em->getRepository(User::class)->find($data->userId)
            : null;

        if ($user === null) {
            throw new UnprocessableEntityHttpException('Invalid userId.');
        }

        $startSemester = $data->startSemesterId
            ? $this->em->getRepository(Semester::class)->find($data->startSemesterId)
            : null;

        if ($startSemester === null) {
            throw new UnprocessableEntityHttpException('Invalid startSemesterId.');
        }

        // Default position to 'Medlem' if not specified
        $position = null;
        if ($data->positionId !== null) {
            $position = $this->em->getRepository(Position::class)->find($data->positionId);
        }
        if ($position === null) {
            $position = $this->em->getRepository(Position::class)->findOneBy(['name' => 'Medlem']);
        }
        if ($position === null) {
            throw new UnprocessableEntityHttpException('Default position "Medlem" not found. Please create it before adding team members.');
        }

        $endSemester = null;
        if ($data->endSemesterId !== null) {
            $endSemester = $this->em->getRepository(Semester::class)->find($data->endSemesterId);
        }

        $membership = new TeamMembership();
        $membership->setTeam($team);
        $membership->setUser($user);
        $membership->setStartSemester($startSemester);
        $membership->setPosition($position);
        if ($endSemester !== null) {
            $membership->setEndSemester($endSemester);
        }

        $this->em->persist($membership);
        $this->em->flush();

        try {
            $this->eventDispatcher->dispatch(new TeamMembershipEvent($membership), TeamMembershipEvent::CREATED);
        } catch (\Throwable $e) {
            $this->logger->error('TeamMembershipEvent::CREATED dispatch failed: '.$e->getMessage());
        }

        return ['id' => $membership->getId()];
    }
}

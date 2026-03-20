<?php

declare(strict_types=1);

namespace App\Organization\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Organization\Infrastructure\Entity\ExecutiveBoardMembership;
use App\Organization\Infrastructure\Repository\ExecutiveBoardRepository;
use App\Shared\Entity\Semester;
use App\Identity\Infrastructure\Entity\User;
use App\Identity\Infrastructure\RoleManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class AdminExecutiveBoardMemberAddProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ExecutiveBoardRepository $executiveBoardRepo,
        private readonly RoleManager $roleManager,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $board = $this->executiveBoardRepo->findBoard();

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

        $endSemester = null;
        if ($data->endSemesterId !== null) {
            $endSemester = $this->em->getRepository(Semester::class)->find($data->endSemesterId);
        }

        $membership = new ExecutiveBoardMembership();
        $membership->setBoard($board);
        $membership->setUser($user);
        $membership->setPositionName($data->positionTitle);
        $membership->setStartSemester($startSemester);
        if ($endSemester !== null) {
            $membership->setEndSemester($endSemester);
        }

        $this->em->persist($membership);
        $this->em->flush();

        try {
            $this->roleManager->updateUserRole($user);
        } catch (\Throwable $e) {
            $this->logger->error('RoleManager::updateUserRole failed after board member add: '.$e->getMessage());
        }

        return ['id' => $membership->getId()];
    }
}

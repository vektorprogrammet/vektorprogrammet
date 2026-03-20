<?php

declare(strict_types=1);

namespace App\Organization\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Organization\Infrastructure\Entity\ExecutiveBoardMembership;
use App\Shared\Entity\Semester;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminExecutiveBoardMemberEditProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $id = $uriVariables['id'] ?? null;
        $membership = $this->em->getRepository(ExecutiveBoardMembership::class)->find($id);

        if ($membership === null) {
            throw new NotFoundHttpException('Executive board membership not found.');
        }

        if ($data->positionTitle !== null) {
            $membership->setPositionName($data->positionTitle);
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

        $this->em->persist($membership);
        $this->em->flush();

        return ['id' => $membership->getId()];
    }
}

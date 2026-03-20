<?php

namespace App\Organization\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Organization\Api\Resource\AdminExecutiveBoardMemberWriteResource;
use App\Organization\Infrastructure\Entity\ExecutiveBoardMembership;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminExecutiveBoardMemberEditProvider implements ProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AdminExecutiveBoardMemberWriteResource
    {
        $id = $uriVariables['id'] ?? null;
        $membership = $id ? $this->em->getRepository(ExecutiveBoardMembership::class)->find($id) : null;

        if ($membership === null) {
            throw new NotFoundHttpException('Executive board membership not found.');
        }

        $resource = new AdminExecutiveBoardMemberWriteResource();
        $resource->id = $membership->getId();
        $resource->positionTitle = $membership->getPositionName();
        $resource->startSemesterId = $membership->getStartSemester()?->getId();
        $resource->endSemesterId = $membership->getEndSemester()?->getId();

        return $resource;
    }
}

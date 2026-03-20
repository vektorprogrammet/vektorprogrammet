<?php

declare(strict_types=1);

namespace App\Organization\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Organization\Api\Resource\AdminExecutiveBoardMemberDeleteResource;
use App\Organization\Infrastructure\Entity\ExecutiveBoardMembership;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminExecutiveBoardMemberDeleteProvider implements ProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AdminExecutiveBoardMemberDeleteResource
    {
        $id = $uriVariables['id'] ?? null;
        $membership = $id !== null ? $this->em->getRepository(ExecutiveBoardMembership::class)->find($id) : null;

        if ($membership === null) {
            throw new NotFoundHttpException('Executive board membership not found.');
        }

        $resource = new AdminExecutiveBoardMemberDeleteResource();
        $resource->id = $membership->getId();

        return $resource;
    }
}

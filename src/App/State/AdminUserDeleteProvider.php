<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\AdminUserDeleteResource;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminUserDeleteProvider implements ProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AdminUserDeleteResource
    {
        $id = $uriVariables['id'] ?? null;
        $user = $id ? $this->em->getRepository(User::class)->find($id) : null;

        if ($user === null) {
            throw new NotFoundHttpException('User not found.');
        }

        $resource = new AdminUserDeleteResource();
        $resource->id = $user->getId();

        return $resource;
    }
}

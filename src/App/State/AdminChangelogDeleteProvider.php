<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\AdminChangelogDeleteResource;
use App\Entity\ChangeLogItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminChangelogDeleteProvider implements ProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AdminChangelogDeleteResource
    {
        $id = $uriVariables['id'] ?? null;
        $item = $id ? $this->em->getRepository(ChangeLogItem::class)->find($id) : null;

        if ($item === null) {
            throw new NotFoundHttpException('Changelog item not found.');
        }

        $resource = new AdminChangelogDeleteResource();
        $resource->id = $item->getId();

        return $resource;
    }
}

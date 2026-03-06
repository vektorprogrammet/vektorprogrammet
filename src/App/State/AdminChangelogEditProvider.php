<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\AdminChangelogWriteResource;
use App\Entity\ChangeLogItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminChangelogEditProvider implements ProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AdminChangelogWriteResource
    {
        $id = $uriVariables['id'] ?? null;
        $item = $id ? $this->em->getRepository(ChangeLogItem::class)->find($id) : null;

        if ($item === null) {
            throw new NotFoundHttpException('Changelog item not found.');
        }

        $resource = new AdminChangelogWriteResource();
        $resource->id = $item->getId();
        $resource->title = $item->getTitle();
        $resource->description = $item->getDescription();
        $resource->date = $item->getDate()?->format('c');
        $resource->githubLink = $item->getGithubLink();

        return $resource;
    }
}

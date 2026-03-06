<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\AdminStaticContentWriteResource;
use App\Entity\StaticContent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminStaticContentEditProvider implements ProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AdminStaticContentWriteResource
    {
        $id = $uriVariables['id'] ?? null;
        $content = $id ? $this->em->getRepository(StaticContent::class)->find($id) : null;

        if ($content === null) {
            throw new NotFoundHttpException('Static content not found.');
        }

        $resource = new AdminStaticContentWriteResource();
        $resource->id = $content->getId();
        $resource->html = $content->getHtml();

        return $resource;
    }
}

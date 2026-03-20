<?php

namespace App\Content\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Content\Api\Resource\AdminSocialEventDeleteResource;
use App\Content\Infrastructure\Entity\SocialEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminSocialEventDeleteProvider implements ProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AdminSocialEventDeleteResource
    {
        $id = $uriVariables['id'] ?? null;
        $event = $id ? $this->em->getRepository(SocialEvent::class)->find($id) : null;

        if ($event === null) {
            throw new NotFoundHttpException('Social event not found.');
        }

        $resource = new AdminSocialEventDeleteResource();
        $resource->id = $event->getId();

        return $resource;
    }
}

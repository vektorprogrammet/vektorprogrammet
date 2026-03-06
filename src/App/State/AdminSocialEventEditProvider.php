<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\AdminSocialEventWriteResource;
use App\Entity\SocialEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminSocialEventEditProvider implements ProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AdminSocialEventWriteResource
    {
        $id = $uriVariables['id'] ?? null;
        $event = $id ? $this->em->getRepository(SocialEvent::class)->find($id) : null;

        if ($event === null) {
            throw new NotFoundHttpException('Social event not found.');
        }

        $resource = new AdminSocialEventWriteResource();
        $resource->id = $event->getId();
        $resource->title = $event->getTitle();
        $resource->description = $event->getDescription();
        $resource->startTime = $event->getStartTime()?->format('c');
        $resource->endTime = $event->getEndTime()?->format('c');
        $resource->departmentId = $event->getDepartment()?->getId();
        $resource->semesterId = $event->getSemester()?->getId();
        $resource->link = $event->getLink();

        return $resource;
    }
}

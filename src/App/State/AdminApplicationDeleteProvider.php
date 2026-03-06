<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\AdminApplicationDeleteResource;
use App\Entity\Application;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminApplicationDeleteProvider implements ProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AdminApplicationDeleteResource
    {
        $id = $uriVariables['id'] ?? null;
        $application = $id ? $this->em->getRepository(Application::class)->find($id) : null;

        if ($application === null) {
            throw new NotFoundHttpException('Application not found.');
        }

        $resource = new AdminApplicationDeleteResource();
        $resource->id = $application->getId();

        return $resource;
    }
}

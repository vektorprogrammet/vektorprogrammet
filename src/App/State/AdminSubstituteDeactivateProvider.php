<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\AdminSubstituteResource;
use App\Entity\Repository\ApplicationRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminSubstituteDeactivateProvider implements ProviderInterface
{
    public function __construct(
        private readonly ApplicationRepository $applicationRepo,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AdminSubstituteResource
    {
        $id = $uriVariables['id'] ?? null;
        $application = $id ? $this->applicationRepo->find($id) : null;

        if ($application === null) {
            throw new NotFoundHttpException('Application not found.');
        }

        $resource = new AdminSubstituteResource();
        $resource->id = $application->getId();

        return $resource;
    }
}

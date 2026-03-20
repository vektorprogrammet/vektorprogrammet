<?php

namespace App\Admission\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Admission\Api\Resource\AdminSubstituteResource;
use App\Admission\Infrastructure\Repository\ApplicationRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminSubstituteEditProvider implements ProviderInterface
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

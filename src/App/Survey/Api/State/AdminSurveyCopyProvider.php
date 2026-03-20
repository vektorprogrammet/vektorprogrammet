<?php

namespace App\Survey\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Survey\Api\Resource\AdminSurveyCopyResource;
use App\Survey\Infrastructure\Repository\SurveyRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminSurveyCopyProvider implements ProviderInterface
{
    public function __construct(
        private readonly SurveyRepository $surveyRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AdminSurveyCopyResource
    {
        $id = $uriVariables['id'] ?? null;
        $survey = $id !== null ? $this->surveyRepository->find($id) : null;

        if ($survey === null) {
            throw new NotFoundHttpException('Survey not found.');
        }

        $resource = new AdminSurveyCopyResource();
        $resource->id = $survey->getId();

        return $resource;
    }
}

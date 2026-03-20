<?php

namespace App\Survey\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Survey\Api\Resource\AdminSurveyDeleteResource;
use App\Survey\Infrastructure\Repository\SurveyRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminSurveyDeleteProvider implements ProviderInterface
{
    public function __construct(
        private readonly SurveyRepository $surveyRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AdminSurveyDeleteResource
    {
        $id = $uriVariables['id'] ?? null;
        $survey = $id ? $this->surveyRepository->find($id) : null;

        if ($survey === null) {
            throw new NotFoundHttpException('Survey not found.');
        }

        $resource = new AdminSurveyDeleteResource();
        $resource->id = $survey->getId();

        return $resource;
    }
}

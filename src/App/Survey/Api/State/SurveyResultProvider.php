<?php

namespace App\Survey\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Survey\Api\Resource\SurveyResultResource;
use App\Survey\Infrastructure\Repository\SurveyRepository;
use App\Identity\Infrastructure\Entity\User;
use App\Survey\Infrastructure\SurveyManager;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SurveyResultProvider implements ProviderInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly SurveyManager $surveyManager,
        private readonly SurveyRepository $surveyRepo,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): SurveyResultResource
    {
        $survey = $this->surveyRepo->find($uriVariables['id']);

        if ($survey === null) {
            throw new NotFoundHttpException('Survey not found.');
        }

        /** @var User $user */
        $user = $this->security->getUser();
        $isAdmin = $this->security->isGranted('ROLE_ADMIN');

        if ($survey->isConfidential() && !$isAdmin) {
            throw new AccessDeniedHttpException('Access denied to confidential survey.');
        }

        if (!$isAdmin && $survey->getDepartment() !== $user->getDepartment()) {
            throw new AccessDeniedHttpException('Access denied to this department\'s survey.');
        }

        $result = $this->surveyManager->surveyResultToJson($survey);

        $resource = new SurveyResultResource();
        $resource->id = $survey->getId();
        $resource->survey = $result['survey'];
        $resource->answers = array_map(function ($taken) {
            if ($taken instanceof \JsonSerializable) {
                return $taken->jsonSerialize();
            }

            // Defensive: all current answer types implement JsonSerializable.
            // This fallback handles potential future non-serializable types.
            return $taken;
        }, $result['answers']);

        return $resource;
    }
}

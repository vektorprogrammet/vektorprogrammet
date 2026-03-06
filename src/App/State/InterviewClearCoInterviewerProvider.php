<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\InterviewClearCoInterviewerResource;
use App\Entity\Repository\InterviewRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InterviewClearCoInterviewerProvider implements ProviderInterface
{
    public function __construct(
        private readonly InterviewRepository $interviewRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): InterviewClearCoInterviewerResource
    {
        $id = $uriVariables['id'] ?? null;
        $interview = $id ? $this->interviewRepository->find($id) : null;

        if ($interview === null) {
            throw new NotFoundHttpException('Interview not found.');
        }

        $resource = new InterviewClearCoInterviewerResource();
        $resource->id = $interview->getId();

        return $resource;
    }
}

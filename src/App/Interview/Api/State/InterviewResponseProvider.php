<?php

namespace App\Interview\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Interview\Api\Resource\InterviewResponseResource;
use App\Interview\Infrastructure\Entity\Interview;
use App\Interview\Infrastructure\Repository\InterviewRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InterviewResponseProvider implements ProviderInterface
{
    public function __construct(
        private readonly InterviewRepository $interviewRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): InterviewResponseResource
    {
        $responseCode = $uriVariables['responseCode'] ?? '';
        $interview = $this->interviewRepository->findByResponseCode($responseCode);

        if (!$interview) {
            throw new NotFoundHttpException('Interview not found.');
        }

        return $this->toResource($interview);
    }

    private function toResource(Interview $interview): InterviewResponseResource
    {
        $resource = new InterviewResponseResource();
        $resource->id = $interview->getId();
        $resource->scheduled = $interview->getScheduled()?->format(\DateTimeInterface::ATOM);
        $resource->room = $interview->getRoom();
        $resource->campus = $interview->getCampus();
        $resource->mapLink = $interview->getMapLink();
        $resource->interviewerName = $interview->getInterviewer()
            ? $interview->getInterviewer()->getFirstName().' '.$interview->getInterviewer()->getLastName()
            : null;
        // interviewerPhone intentionally omitted — not shown in legacy template, avoid exposing staff PII
        $resource->status = $interview->getInterviewStatusAsString();
        $resource->responseCode = $interview->getResponseCode(); // identifier — required for API Platform IRI generation

        return $resource;
    }
}

<?php

namespace App\Admission\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Admission\Api\Resource\ApplicationDetailResource;
use App\Admission\Infrastructure\Entity\Application;
use App\Admission\Infrastructure\Repository\ApplicationRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApplicationDetailProvider implements ProviderInterface
{
    public function __construct(
        private readonly ApplicationRepository $applicationRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ApplicationDetailResource
    {
        $id = $uriVariables['id'] ?? null;
        $application = $this->applicationRepository->find($id);

        if (!$application) {
            throw new NotFoundHttpException('Application not found.');
        }

        return $this->toResource($application);
    }

    private function toResource(Application $application): ApplicationDetailResource
    {
        $resource = new ApplicationDetailResource();
        $resource->id = $application->getId();

        $user = $application->getUser();
        $resource->userName = $user->getFirstName().' '.$user->getLastName();
        $resource->userEmail = $user->getEmail();
        $resource->userPhone = $user->getPhone();

        $resource->previousParticipation = $application->getPreviousParticipation();
        $resource->yearOfStudy = $application->getYearOfStudy();
        $resource->monday = $application->isMonday();
        $resource->tuesday = $application->isTuesday();
        $resource->wednesday = $application->isWednesday();
        $resource->thursday = $application->isThursday();
        $resource->friday = $application->isFriday();
        $resource->heardAboutFrom = $application->getHeardAboutFrom();
        $resource->language = $application->getLanguage();
        $resource->preferredGroup = $application->getPreferredGroup();
        $resource->doublePosition = $application->getDoublePosition();
        $resource->teamInterest = $application->getTeamInterest();
        $resource->substitute = $application->isSubstitute();

        $interview = $application->getInterview();
        $resource->interviewScheduled = $interview?->getScheduled()->format(\DateTimeInterface::ATOM);
        $resource->interviewStatus = $interview?->getInterviewStatusAsString();

        $resource->created = $application->getCreated()->format(\DateTimeInterface::ATOM);

        return $resource;
    }
}

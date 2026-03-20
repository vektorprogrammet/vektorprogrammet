<?php

namespace App\Survey\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Survey\Api\Resource\SurveyPopupResource;
use App\Shared\Repository\SemesterRepository;
use App\Survey\Infrastructure\Repository\SurveyRepository;
use App\Identity\Infrastructure\Entity\User;
use App\Identity\Domain\Roles;
use App\Identity\Infrastructure\RoleManager;
use Symfony\Bundle\SecurityBundle\Security;

class SurveyPopupProvider implements ProviderInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly SurveyRepository $surveyRepo,
        private readonly SemesterRepository $semesterRepo,
        private readonly RoleManager $roleManager,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?SurveyPopupResource
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return new SurveyPopupResource();
        }

        if (!$this->roleManager->userIsGranted($user, Roles::TEAM_MEMBER)) {
            return new SurveyPopupResource();
        }

        if ($user->getReservedFromPopUp()) {
            return new SurveyPopupResource();
        }

        $lastPopUp = $user->getLastPopUpTime();
        $now = new \DateTime();
        if ($lastPopUp->diff($now)->days < 1) {
            return new SurveyPopupResource();
        }

        $semester = $this->semesterRepo->findCurrentSemester();

        $surveys = $this->surveyRepo->findAllNotTakenByUserAndSemester($user, $semester);
        if ($surveys === []) {
            return new SurveyPopupResource();
        }

        $survey = end($surveys);
        $resource = new SurveyPopupResource();
        $resource->id = $survey->getId();
        $resource->name = $survey->getName();

        return $resource;
    }
}

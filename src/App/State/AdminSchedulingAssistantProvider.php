<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\AdminSchedulingAssistantResource;
use App\Entity\Repository\AdmissionPeriodRepository;
use App\Entity\Repository\ApplicationRepository;
use App\Shared\Repository\SemesterRepository;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;

class AdminSchedulingAssistantProvider implements ProviderInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly AdmissionPeriodRepository $admissionPeriodRepo,
        private readonly ApplicationRepository $applicationRepo,
        private readonly SemesterRepository $semesterRepo,
    ) {
    }

    /**
     * @return AdminSchedulingAssistantResource[]
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return [];
        }

        $department = $user->getDepartment();
        $currentSemester = $this->semesterRepo->findOrCreateCurrentSemester();

        $admissionPeriod = $this->admissionPeriodRepo
            ->findOneByDepartmentAndSemester($department, $currentSemester);

        if ($admissionPeriod === null) {
            return [];
        }

        $applications = $this->applicationRepo
            ->findAllAllocatableApplicationsByAdmissionPeriod($admissionPeriod);

        $results = [];
        foreach ($applications as $application) {
            $resource = new AdminSchedulingAssistantResource();
            $resource->id = $application->getId();
            $resource->name = $application->getUser()->getFullName();
            $resource->email = $application->getUser()->getEmail();
            $resource->doublePosition = $application->getDoublePosition();

            $preferredGroup = null;
            switch ($application->getPreferredGroup()) {
                case 'Bolk 1':
                    $preferredGroup = 1;
                    break;
                case 'Bolk 2':
                    $preferredGroup = 2;
                    break;
            }
            if ($application->getDoublePosition()) {
                $preferredGroup = null;
            }
            $resource->preferredGroup = $preferredGroup;

            $resource->availability = [
                'Monday' => $application->isMonday(),
                'Tuesday' => $application->isTuesday(),
                'Wednesday' => $application->isWednesday(),
                'Thursday' => $application->isThursday(),
                'Friday' => $application->isFriday(),
            ];

            $resource->previousParticipation = $application->getPreviousParticipation();

            if ($application->getPreviousParticipation()) {
                $resource->suitability = 'Ja';
                $resource->score = 20;
            } else {
                $interview = $application->getInterview();
                if ($interview !== null && $interview->getInterviewScore() !== null) {
                    $resource->score = $interview->getScore();
                    $resource->suitability = $interview->getInterviewScore()->getSuitableAssistant();
                }
            }

            $resource->language = $application->getLanguage();

            $results[] = $resource;
        }

        return $results;
    }
}

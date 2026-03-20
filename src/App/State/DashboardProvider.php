<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\DashboardResource;
use App\Entity\Repository\AdmissionPeriodRepository;
use App\Entity\Repository\ApplicationRepository;
use App\Entity\Repository\AssistantHistoryRepository;
use App\Shared\Repository\SemesterRepository;
use App\Entity\User;
use App\Service\ApplicationManager;
use Symfony\Bundle\SecurityBundle\Security;

class DashboardProvider implements ProviderInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly ApplicationRepository $applicationRepo,
        private readonly AdmissionPeriodRepository $admissionPeriodRepo,
        private readonly AssistantHistoryRepository $assistantHistoryRepo,
        private readonly SemesterRepository $semesterRepo,
        private readonly ApplicationManager $applicationManager,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): DashboardResource
    {
        /** @var User $user */
        $user = $this->security->getUser();

        $dashboard = new DashboardResource();
        $dashboard->firstName = $user->getFirstName();
        $dashboard->lastName = $user->getLastName();
        $dashboard->email = $user->getEmail();

        // Find active application
        $department = $user->getDepartment();
        $semester = $this->semesterRepo->findOrCreateCurrentSemester();
        $admissionPeriod = $this->admissionPeriodRepo
            ->findOneByDepartmentAndSemester($department, $semester);

        if ($admissionPeriod !== null) {
            $application = $this->applicationRepo
                ->findByUserInAdmissionPeriod($user, $admissionPeriod);

            if ($application !== null) {
                $status = $this->applicationManager->getApplicationStatus($application);
                $interview = $application->getInterview();

                $dashboard->activeApplication = [
                    'id' => $application->getId(),
                    'status' => $status->getStep(),
                    'statusTitle' => $status->getText(),
                    'statusDescription' => $status->getNextAction(),
                    'interviewScheduled' => $interview?->getScheduled()?->format('c'),
                    'interviewRoom' => $interview?->getRoom(),
                ];
            }
        }

        // Find active assistant histories
        $activeHistories = $this->assistantHistoryRepo->findActiveAssistantHistoriesByUser($user);
        $dashboard->activeAssistantHistories = array_map(
            fn ($history) => [
                'school' => $history->getSchool()->getName(),
                'semester' => $history->getSemester()->getName(),
                'group' => $history->getBolk(),
            ],
            $activeHistories
        );

        return $dashboard;
    }
}

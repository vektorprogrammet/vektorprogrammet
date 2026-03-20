<?php

namespace App\Admission\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Admission\Api\Resource\AdmissionStatisticsResource;
use App\Admission\Infrastructure\Repository\AdmissionPeriodRepository;
use App\Admission\Infrastructure\Repository\ApplicationRepository;
use App\Operations\Infrastructure\Repository\AssistantHistoryRepository;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class AdmissionStatisticsProvider implements ProviderInterface
{
    public function __construct(
        private readonly AdmissionPeriodRepository $admissionPeriodRepo,
        private readonly ApplicationRepository $applicationRepo,
        private readonly AssistantHistoryRepository $assistantHistoryRepo,
        private readonly DepartmentRepository $departmentRepo,
        private readonly RequestStack $requestStack,
        private readonly Security $security,
        private readonly SemesterRepository $semesterRepo,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AdmissionStatisticsResource
    {
        $request = $this->requestStack->getCurrentRequest();

        /** @var User $user */
        $user = $this->security->getUser();

        // Resolve department: from query param or user's department
        $departmentId = $request?->query->get('department');
        $department = $departmentId !== null
            ? $this->departmentRepo->find((int) $departmentId)
            : $user->getFieldOfStudy()?->getDepartment();

        // Resolve semester: from query param or current semester
        $semesterId = $request?->query->get('semester');
        $semester = $semesterId !== null
            ? $this->semesterRepo->find((int) $semesterId)
            : $this->semesterRepo->findOrCreateCurrentSemester();

        $resource = new AdmissionStatisticsResource();
        $resource->departmentName = $department ? $department->getName() : '';
        $resource->semesterName = $semester ? $semester->getName() : '';

        // Application statistics
        $admissionPeriod = ($department !== null && $semester !== null)
            ? $this->admissionPeriodRepo->findOneByDepartmentAndSemester($department, $semester)
            : null;

        if ($admissionPeriod !== null) {
            $resource->applicationCount = (int) $this->applicationRepo->numOfApplications($admissionPeriod);
            $resource->maleApplications = (int) $this->applicationRepo->numOfGender($admissionPeriod, 0);
            $resource->femaleApplications = (int) $this->applicationRepo->numOfGender($admissionPeriod, 1);
        }

        // Assistant history statistics
        if ($department !== null && $semester !== null) {
            $assistantHistories = $this->assistantHistoryRepo->findByDepartmentAndSemester($department, $semester);
            $resource->assistantCount = count($assistantHistories);

            // Gender counting depends on User::getGender() returning strict bool (false=male, true=female).
            // Coverage of these branches depends on fixture data having gendered assistant histories.
            $maleCount = 0;
            $femaleCount = 0;
            foreach ($assistantHistories as $history) {
                $gender = $history->getUser()->getGender();
                if ($gender === false) {
                    ++$maleCount;
                } elseif ($gender === true) {
                    ++$femaleCount;
                }
            }
            $resource->maleAssistants = $maleCount;
            $resource->femaleAssistants = $femaleCount;
        }

        return $resource;
    }
}

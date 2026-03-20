<?php

namespace App\Admission\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Admission\Api\Resource\AdminSubstituteResource;
use App\Admission\Infrastructure\Repository\AdmissionPeriodRepository;
use App\Admission\Infrastructure\Repository\ApplicationRepository;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class AdminSubstituteListProvider implements ProviderInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly AdmissionPeriodRepository $admissionPeriodRepo,
        private readonly ApplicationRepository $applicationRepo,
        private readonly DepartmentRepository $departmentRepo,
        private readonly SemesterRepository $semesterRepo,
        private readonly RequestStack $requestStack,
    ) {
    }

    /**
     * @return AdminSubstituteResource[]
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return [];
        }

        $request = $this->requestStack->getCurrentRequest();

        // Resolve department: from query param or user's department
        $departmentId = $request?->query->get('department');
        if ($departmentId !== null) {
            $department = $this->departmentRepo->find($departmentId);
        } else {
            $department = $user->getDepartment();
        }

        if ($department === null) {
            return [];
        }

        // Resolve semester: from query param or current semester
        $semesterId = $request?->query->get('semester');
        if ($semesterId !== null) {
            $semester = $this->semesterRepo->find($semesterId);
        } else {
            $semester = $this->semesterRepo->findOrCreateCurrentSemester();
        }

        if ($semester === null) {
            return [];
        }

        $admissionPeriod = $this->admissionPeriodRepo
            ->findOneByDepartmentAndSemester($department, $semester);

        if ($admissionPeriod === null) {
            return [];
        }

        $substitutes = $this->applicationRepo
            ->findSubstitutesByAdmissionPeriod($admissionPeriod);

        $results = [];
        foreach ($substitutes as $application) {
            $resource = new AdminSubstituteResource();
            $resource->id = $application->getId();
            $resource->name = $application->getUser()->getFullName();
            $resource->email = $application->getUser()->getEmail();
            $resource->yearOfStudy = $application->getYearOfStudy();
            $resource->language = $application->getLanguage();
            $resource->monday = $application->isMonday();
            $resource->tuesday = $application->isTuesday();
            $resource->wednesday = $application->isWednesday();
            $resource->thursday = $application->isThursday();
            $resource->friday = $application->isFriday();

            $results[] = $resource;
        }

        return $results;
    }
}

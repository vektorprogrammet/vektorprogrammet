<?php

namespace App\Admission\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Admission\Api\Resource\AdminApplicationListResource;
use App\Admission\Infrastructure\Entity\Application;
use App\Admission\Infrastructure\Repository\AdmissionPeriodRepository;
use App\Admission\Infrastructure\Repository\ApplicationRepository;
use App\Identity\Infrastructure\AccessControlService;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Identity\Infrastructure\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class AdminApplicationListProvider implements ProviderInterface
{
    public function __construct(
        private readonly AccessControlService $accessControl,
        private readonly Security $security,
        private readonly ApplicationRepository $applicationRepo,
        private readonly AdmissionPeriodRepository $admissionPeriodRepo,
        private readonly DepartmentRepository $departmentRepo,
        private readonly SemesterRepository $semesterRepo,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AdminApplicationListResource
    {
        $request = $this->requestStack->getCurrentRequest();
        $status = $request?->query->get('status', 'new') ?? 'new';
        $departmentId = $request?->query->get('department');
        $semesterId = $request?->query->get('semester');

        /** @var User $user */
        $user = $this->security->getUser();

        // Resolve department
        $department = $departmentId
            ? $this->departmentRepo->find((int) $departmentId)
            : $user->getDepartment();

        if ($department === null) {
            return $this->emptyResult($status);
        }

        $this->accessControl->assertDepartmentAccess($department, $user);

        // Resolve semester
        $semester = $semesterId
            ? $this->semesterRepo->find((int) $semesterId)
            : $this->semesterRepo->findOrCreateCurrentSemester();

        if ($semester === null) {
            return $this->emptyResult($status);
        }

        // Find admission period
        $admissionPeriod = $this->admissionPeriodRepo->findOneByDepartmentAndSemester($department, $semester);

        if ($admissionPeriod === null) {
            return $this->emptyResult($status);
        }

        // Switch on status to call appropriate repository method
        $applications = match ($status) {
            'new' => $this->applicationRepo->findNewApplicationsByAdmissionPeriod($admissionPeriod),
            'assigned' => $this->applicationRepo->findAssignedApplicants($admissionPeriod),
            'interviewed' => $this->applicationRepo->findInterviewedApplicants($admissionPeriod),
            'existing' => $this->applicationRepo->findExistingApplicants($admissionPeriod),
            default => [],
        };

        $resource = new AdminApplicationListResource();
        $resource->status = $status;
        $resource->applications = array_map(fn (Application $app) => $this->mapApplication($app), $applications);

        return $resource;
    }

    private function emptyResult(string $status): AdminApplicationListResource
    {
        $resource = new AdminApplicationListResource();
        $resource->status = $status;
        $resource->applications = [];

        return $resource;
    }

    private function mapApplication(Application $app): array
    {
        $user = $app->getUser();
        $interview = $app->getInterview();
        $interviewer = $interview?->getInterviewer();

        return [
            'id' => $app->getId(),
            'userName' => $user->getFirstName().' '.$user->getLastName(),
            'userEmail' => $user->getEmail(),
            'interviewStatus' => $interview?->getInterviewStatusAsString(),
            'interviewScheduled' => $interview?->getScheduled()?->format('c'),
            'interviewer' => $interviewer ? $interviewer->getFirstName().' '.$interviewer->getLastName() : null,
            'previousParticipation' => $app->getPreviousParticipation(),
        ];
    }
}

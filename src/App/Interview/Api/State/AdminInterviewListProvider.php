<?php

namespace App\Interview\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Interview\Api\Resource\AdminInterviewListResource;
use App\Admission\Infrastructure\Entity\Application;
use App\Admission\Infrastructure\Repository\AdmissionPeriodRepository;
use App\Admission\Infrastructure\Repository\ApplicationRepository;
use App\Entity\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminInterviewListProvider implements ProviderInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly RequestStack $requestStack,
        private readonly ApplicationRepository $applicationRepo,
        private readonly AdmissionPeriodRepository $admissionPeriodRepo,
        private readonly DepartmentRepository $departmentRepo,
        private readonly SemesterRepository $semesterRepo,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AdminInterviewListResource
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $request = $this->requestStack->getCurrentRequest();

        $departmentId = $request?->query->get('department');
        if ($departmentId !== null) {
            $department = $this->departmentRepo->find($departmentId);
            if ($department === null) {
                throw new NotFoundHttpException('Department not found.');
            }
        } else {
            $department = $user->getDepartment();
        }

        if ($department !== $user->getDepartment() && !$this->security->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException('You can only view your own department.');
        }

        $semesterId = $request?->query->get('semester');
        if ($semesterId !== null) {
            $semester = $this->semesterRepo->find($semesterId);
            if ($semester === null) {
                throw new NotFoundHttpException('Semester not found.');
            }
        } else {
            $semester = $this->semesterRepo->findOrCreateCurrentSemester();
        }

        $admissionPeriod = $this->admissionPeriodRepo->findOneByDepartmentAndSemester($department, $semester);

        $resource = new AdminInterviewListResource();

        if ($admissionPeriod === null) {
            return $resource;
        }

        $seen = [];

        // Assigned (pending) interviews
        $assigned = $this->applicationRepo->findAssignedApplicants($admissionPeriod);
        foreach ($assigned as $application) {
            $this->addInterviewEntry($resource, $application, $seen);
        }

        // Interviewed (completed) interviews
        $interviewed = $this->applicationRepo->findInterviewedApplicants($admissionPeriod);
        foreach ($interviewed as $application) {
            $this->addInterviewEntry($resource, $application, $seen);
        }

        return $resource;
    }

    /**
     * @param array<int|null, bool> $seen
     *
     * Note: The null-interview guard (line ~92) and dedup guard (line ~97) are defensive
     * checks. In practice, queries only return applications with interviews, and each
     * interview maps to exactly one application. These guards protect against data
     * anomalies without practical test coverage.
     */
    private function addInterviewEntry(AdminInterviewListResource $resource, Application $application, array &$seen): void
    {
        $interview = $application->getInterview();
        if ($interview === null) {
            return;
        }

        $interviewId = $interview->getId();
        if (isset($seen[$interviewId])) {
            return;
        }
        $seen[$interviewId] = true;

        $applicant = $application->getUser();
        $interviewer = $interview->getInterviewer();
        $coInterviewer = $interview->getCoInterviewer();

        $resource->interviews[] = [
            'id' => $interviewId,
            'applicantName' => $applicant->getFirstName().' '.$applicant->getLastName(),
            'interviewerName' => $interviewer ? $interviewer->getFirstName().' '.$interviewer->getLastName() : null,
            'scheduled' => $interview->getScheduled()?->format(\DateTimeInterface::ATOM),
            'status' => $interview->getInterviewStatusAsString(),
            'interviewed' => $interview->getInterviewed(),
            'coInterviewer' => $coInterviewer ? $coInterviewer->getFirstName().' '.$coInterviewer->getLastName() : null,
        ];
    }
}

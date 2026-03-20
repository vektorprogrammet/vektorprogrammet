<?php

declare(strict_types=1);

namespace App\Organization\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Organization\Api\Resource\TeamInterestResource;
use App\Admission\Infrastructure\Repository\AdmissionPeriodRepository;
use App\Admission\Infrastructure\Repository\ApplicationRepository;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Organization\Infrastructure\Repository\TeamRepository;
use App\Identity\Infrastructure\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TeamInterestProvider implements ProviderInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly RequestStack $requestStack,
        private readonly ApplicationRepository $applicationRepo,
        private readonly TeamRepository $teamRepo,
        private readonly AdmissionPeriodRepository $admissionPeriodRepo,
        private readonly DepartmentRepository $departmentRepo,
        private readonly SemesterRepository $semesterRepo,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): TeamInterestResource
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

        // Only admins can view other departments
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

        $resource = new TeamInterestResource();

        if ($admissionPeriod === null) {
            return $resource;
        }

        $applications = $this->applicationRepo->findApplicationByTeamInterestAndAdmissionPeriod($admissionPeriod);
        $teams = $this->teamRepo->findByTeamInterestAndAdmissionPeriod($admissionPeriod);

        foreach ($applications as $application) {
            $appUser = $application->getUser();
            $potentialTeams = $application->getPotentialTeams();

            $teamData = [];
            foreach ($potentialTeams as $team) {
                $teamData[] = [
                    'id' => $team->getId(),
                    'name' => $team->getName(),
                ];
            }

            $resource->applicants[] = [
                'id' => $application->getId(),
                'name' => $appUser->getFirstName().' '.$appUser->getLastName(),
                'teams' => $teamData,
            ];
        }

        foreach ($teams as $team) {
            $resource->teams[] = [
                'id' => $team->getId(),
                'name' => $team->getName(),
            ];
        }

        return $resource;
    }
}

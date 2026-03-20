<?php

namespace App\Survey\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Survey\Api\Resource\AdminSurveyListResource;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Survey\Infrastructure\Repository\SurveyRepository;
use App\Survey\Infrastructure\Repository\SurveyTakenRepository;
use App\Survey\Infrastructure\Entity\Survey;
use App\Identity\Infrastructure\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class AdminSurveyListProvider implements ProviderInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly SurveyRepository $surveyRepo,
        private readonly SurveyTakenRepository $surveyTakenRepo,
        private readonly DepartmentRepository $departmentRepo,
        private readonly SemesterRepository $semesterRepo,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AdminSurveyListResource
    {
        $request = $this->requestStack->getCurrentRequest();
        $departmentId = $request?->query->get('department');
        $semesterId = $request?->query->get('semester');

        /** @var User $user */
        $user = $this->security->getUser();

        // Resolve department
        $department = $departmentId
            ? $this->departmentRepo->find((int) $departmentId)
            : $user->getDepartment();

        if ($department === null) {
            $resource = new AdminSurveyListResource();
            $resource->surveys = [];

            return $resource;
        }

        // Resolve semester
        $semester = $semesterId
            ? $this->semesterRepo->find((int) $semesterId)
            : $this->semesterRepo->findOrCreateCurrentSemester();

        if ($semester === null) {
            $resource = new AdminSurveyListResource();
            $resource->surveys = [];

            return $resource;
        }

        // Query surveys for this department and semester, ordered by ID descending
        $surveys = $this->surveyRepo->findBy(
            ['semester' => $semester, 'department' => $department],
            ['id' => 'DESC']
        );

        $resource = new AdminSurveyListResource();
        $resource->surveys = array_map(fn (Survey $survey) => $this->mapSurvey($survey), $surveys);

        return $resource;
    }

    private function mapSurvey(Survey $survey): array
    {
        return [
            'id' => $survey->getId(),
            'name' => $survey->getName(),
            'targetAudience' => $survey->getTargetAudience(),
            'confidential' => $survey->isConfidential(),
            'totalAnswered' => $this->surveyTakenRepo->count(['survey' => $survey]),
        ];
    }
}

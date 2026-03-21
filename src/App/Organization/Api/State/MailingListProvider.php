<?php

namespace App\Organization\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Identity\Infrastructure\AccessControlService;
use App\Organization\Api\Resource\MailingListResource;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Identity\Infrastructure\Repository\UserRepository;
use App\Identity\Infrastructure\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class MailingListProvider implements ProviderInterface
{
    public function __construct(
        private readonly AccessControlService $accessControl,
        private readonly Security $security,
        private readonly UserRepository $userRepo,
        private readonly DepartmentRepository $departmentRepo,
        private readonly SemesterRepository $semesterRepo,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): MailingListResource
    {
        $request = $this->requestStack->getCurrentRequest();
        $type = $request?->query->get('type', 'assistants') ?? 'assistants';
        $departmentId = $request?->query->get('department');
        $semesterId = $request?->query->get('semester');

        /** @var User $currentUser */
        $currentUser = $this->security->getUser();

        // Resolve department
        $department = $departmentId
            ? $this->departmentRepo->find((int) $departmentId)
            : $currentUser->getDepartment();

        if ($department === null) {
            return $this->emptyResult($type);
        }

        $this->accessControl->assertDepartmentAccess($department, $currentUser);

        // Resolve semester
        $semester = $semesterId
            ? $this->semesterRepo->find((int) $semesterId)
            : $this->semesterRepo->findOrCreateCurrentSemester();

        if ($semester === null) {
            return $this->emptyResult($type);
        }

        // Get users based on type
        $users = match ($type) {
            'assistants' => $this->userRepo->findUsersWithAssistantHistoryInDepartmentAndSemester($department, $semester),
            'team' => $this->userRepo->findUsersInDepartmentWithTeamMembershipInSemester($department, $semester),
            'all' => $this->mergeAndDeduplicate(
                $this->userRepo->findUsersWithAssistantHistoryInDepartmentAndSemester($department, $semester),
                $this->userRepo->findUsersInDepartmentWithTeamMembershipInSemester($department, $semester),
            ),
            default => [],
        };

        $mapped = array_map(fn (User $user) => [
            'name' => $user->getFirstName().' '.$user->getLastName(),
            'email' => $user->getEmail(),
        ], $users);

        $resource = new MailingListResource();
        $resource->type = $type;
        $resource->users = $mapped;
        $resource->count = count($mapped);

        return $resource;
    }

    private function emptyResult(string $type): MailingListResource
    {
        $resource = new MailingListResource();
        $resource->type = $type;
        $resource->users = [];
        $resource->count = 0;

        return $resource;
    }

    /**
     * @param User[] $assistants
     * @param User[] $teamMembers
     *
     * @return User[]
     */
    private function mergeAndDeduplicate(array $assistants, array $teamMembers): array
    {
        $seen = [];
        $result = [];

        foreach ([...$assistants, ...$teamMembers] as $user) {
            $id = $user->getId();
            if (!isset($seen[$id])) {
                $seen[$id] = true;
                $result[] = $user;
            }
        }

        return $result;
    }
}

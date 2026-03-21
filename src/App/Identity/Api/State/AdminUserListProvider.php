<?php

namespace App\Identity\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Identity\Api\Resource\AdminUserListResource;
use App\Identity\Infrastructure\AccessControlService;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Identity\Infrastructure\Repository\UserRepository;
use App\Identity\Infrastructure\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class AdminUserListProvider implements ProviderInterface
{
    public function __construct(
        private readonly AccessControlService $accessControl,
        private readonly Security $security,
        private readonly DepartmentRepository $departmentRepository,
        private readonly RequestStack $requestStack,
        private readonly UserRepository $userRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AdminUserListResource
    {
        $request = $this->requestStack->getCurrentRequest();
        $departmentId = $request?->query->get('department');

        /** @var \App\Identity\Infrastructure\Entity\User $user */
        $user = $this->security->getUser();

        if ($departmentId) {
            $department = $this->departmentRepository->find($departmentId);
        } else {
            $activeDepartments = $this->departmentRepository->findActive();
            $department = $activeDepartments[0] ?? null;
        }

        $resource = new AdminUserListResource();

        if (!$department) {
            return $resource;
        }

        $this->accessControl->assertDepartmentAccess($department, $user);

        $resource->departmentName = $department->getName();

        $activeUsers = $this->userRepository->findAllActiveUsersByDepartment($department);
        $inactiveUsers = $this->userRepository->findAllInActiveUsersByDepartment($department);

        $resource->activeUsers = array_map(fn (User $user) => $this->mapUser($user), $activeUsers);
        $resource->inactiveUsers = array_map(fn (User $user) => $this->mapUser($user), $inactiveUsers);

        return $resource;
    }

    private function mapUser(User $user): array
    {
        return [
            'id' => $user->getId(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'email' => $user->getEmail(),
            'phone' => $user->getPhone(),
            'role' => $user->getRoles()[0] ?? 'ROLE_USER',
        ];
    }
}

<?php

namespace App\Identity\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Identity\Api\Resource\AdminUserListResource;
use App\Identity\Infrastructure\Entity\User;
use App\Identity\Infrastructure\Repository\UserRepository;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class AdminUserListProvider implements ProviderInterface
{
    public function __construct(
        private readonly DepartmentRepository $departmentRepository,
        private readonly RequestStack $requestStack,
        private readonly UserRepository $userRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AdminUserListResource
    {
        $request = $this->requestStack->getCurrentRequest();
        $departmentId = $request?->query->get('department');

        if ($departmentId !== null && $departmentId !== '') {
            $department = $this->departmentRepository->find($departmentId);
        } else {
            $activeDepartments = $this->departmentRepository->findActive();
            $department = $activeDepartments[0] ?? null;
        }

        $resource = new AdminUserListResource();

        if (!$department) {
            return $resource;
        }

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

<?php

namespace App\Identity\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Identity\Infrastructure\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PublicUserProfileProvider implements ProviderInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object
    {
        $user = $this->userRepository->find($uriVariables['id'] ?? 0);

        if (!$user || !$user->isActive()) {
            throw new NotFoundHttpException('User not found.');
        }

        if ($user->getTeamMemberships()->isEmpty() && $user->getExecutiveBoardMemberships()->isEmpty()) {
            throw new NotFoundHttpException('User not found.');
        }

        return $user;
    }
}

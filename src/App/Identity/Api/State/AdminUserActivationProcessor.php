<?php

namespace App\Identity\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Identity\Infrastructure\Entity\User;
use App\Identity\Infrastructure\UserRegistration;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminUserActivationProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserRegistration $userRegistration,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $id = $uriVariables['id'] ?? null;
        $user = $this->em->getRepository(User::class)->find($id);

        if ($user === null) {
            throw new NotFoundHttpException('User not found.');
        }

        try {
            $this->userRegistration->sendActivationCode($user);
        } catch (\Throwable $e) {
            $this->logger->error('Failed to send activation email: '.$e->getMessage());
        }

        return ['success' => true];
    }
}

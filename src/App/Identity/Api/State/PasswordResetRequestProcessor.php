<?php

namespace App\Identity\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Identity\Api\Resource\PasswordResetRequest;
use App\Identity\Infrastructure\Repository\PasswordResetRepository;
use App\Identity\Infrastructure\PasswordManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class PasswordResetRequestProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly PasswordManager $passwordManager,
        private readonly PasswordResetRepository $passwordResetRepo,
        private readonly EntityManagerInterface $em,
        private readonly RateLimiterFactory $passwordResetLimiter,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        assert($data instanceof PasswordResetRequest);

        $limiter = $this->passwordResetLimiter->create($this->requestStack->getCurrentRequest()?->getClientIp() ?? 'unknown');
        if (false === $limiter->consume(1)->isAccepted()) {
            throw new TooManyRequestsHttpException();
        }

        $email = $data->email;

        // Block company emails
        if (str_ends_with($email, '@vektorprogrammet.no')) {
            throw new UnprocessableEntityHttpException('Kan ikke resette passord med "@vektorprogrammet.no"-adresse.');
        }

        $passwordReset = $this->passwordManager->createPasswordResetEntity($email);

        // Silent return for non-existent or inactive users (prevent enumeration)
        if ($passwordReset === null) {
            return;
        }
        if (!$passwordReset->getUser()->isActive()) {
            return;
        }

        // Remove old reset codes for this user
        $oldResets = $this->passwordResetRepo->findByUser($passwordReset->getUser());
        foreach ($oldResets as $old) {
            $this->em->remove($old);
        }

        $this->em->persist($passwordReset);
        $this->em->flush();

        $this->passwordManager->sendResetCode($passwordReset);
    }
}

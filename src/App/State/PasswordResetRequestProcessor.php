<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\PasswordResetRequest;
use App\Entity\Repository\PasswordResetRepository;
use App\Service\PasswordManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class PasswordResetRequestProcessor implements ProcessorInterface
{
    public function __construct(
        private PasswordManager $passwordManager,
        private PasswordResetRepository $passwordResetRepo,
        private EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        assert($data instanceof PasswordResetRequest);

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

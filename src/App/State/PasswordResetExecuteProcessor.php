<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\PasswordResetExecute;
use App\Service\PasswordManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class PasswordResetExecuteProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly PasswordManager $passwordManager,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        assert($data instanceof PasswordResetExecute);

        $code = $uriVariables['code'] ?? '';

        if (!$this->passwordManager->resetCodeIsValid($code)) {
            throw new UnprocessableEntityHttpException('Ugyldig eller utløpt kode.');
        }

        if ($this->passwordManager->resetCodeHasExpired($code)) {
            throw new UnprocessableEntityHttpException('Ugyldig eller utløpt kode.');
        }

        $passwordReset = $this->passwordManager->getPasswordResetByResetCode($code);
        $user = $passwordReset->getUser();

        $user->setPassword($data->password);

        $this->em->remove($passwordReset);
        $this->em->persist($user);
        $this->em->flush();
    }
}

<?php

namespace App\Service;

use App\Entity\PasswordReset;
use App\Entity\User;
use App\Mailer\MailerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class PasswordManager
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly MailerInterface $mailer,
        private readonly Environment $twig,
    ) {
    }

    public function generateRandomResetCode(): string
    {
        return bin2hex(openssl_random_pseudo_bytes(12));
    }

    public function hashCode(string $resetCode): string
    {
        return hash('sha512', $resetCode, false);
    }

    public function resetCodeIsValid(string $resetCode): bool
    {
        $hashedResetCode = $this->hashCode($resetCode);
        $passwordReset = $this->em->getRepository(PasswordReset::class)->findPasswordResetByHashedResetCode($hashedResetCode);

        return $passwordReset !== null && $passwordReset->getUser() !== null;
    }

    public function resetCodeHasExpired(string $resetCode): bool
    {
        $hashedResetCode = $this->hashCode($resetCode);
        $passwordReset = $this->em->getRepository(PasswordReset::class)->findPasswordResetByHashedResetCode($hashedResetCode);

        $currentTime = new \DateTime();
        $timeDifference = date_diff($passwordReset->getResetTime(), $currentTime);

        $hasExpired = $timeDifference->d > 1;

        if ($hasExpired) {
            $this->em->getRepository(PasswordReset::class)->deletePasswordResetByHashedResetCode($hashedResetCode);
        }

        return $hasExpired;
    }

    public function getPasswordResetByResetCode(string $resetCode): PasswordReset
    {
        $hashedResetCode = $this->hashCode($resetCode);

        return $this->em->getRepository(PasswordReset::class)->findPasswordResetByHashedResetCode($hashedResetCode);
    }

    public function createPasswordResetEntity(string $email)
    {
        $passwordReset = new PasswordReset();

        // Finds the user based on the email
        $user = $this->em->getRepository(User::class)->findUserByEmail($email);

        if ($user === null) {
            return null;
        }

        // Creates a random hex-string as reset code
        $resetCode = $this->generateRandomResetCode();

        // Hashes the random reset code to store in the database
        $hashedResetCode = $this->hashCode($resetCode);

        // Adds the info in the passwordReset entity
        $passwordReset->setUser($user);
        $passwordReset->setResetCode($resetCode);
        $passwordReset->setHashedResetCode($hashedResetCode);

        return $passwordReset;
    }

    public function sendResetCode(PasswordReset $passwordReset)
    {
        // Sends a email with the url for resetting the password
        $emailMessage = (new Email())
            ->subject('Tilbakestill passord for vektorprogrammet.no')
            ->from(new Address('ikkesvar@vektorprogrammet.no', 'Vektorprogrammet'))
            ->to($passwordReset->getUser()->getEmail())
            ->text($this->twig->render('reset_password/new_password_email.txt.twig', [
                'resetCode' => $passwordReset->getResetCode(),
                'user' => $passwordReset->getUser(),
            ]));
        $this->mailer->send($emailMessage);
    }
}

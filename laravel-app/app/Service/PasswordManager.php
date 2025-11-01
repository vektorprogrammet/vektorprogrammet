<?php


namespace App\Service;

use App\Models\PasswordReset;
use App\Models\User;
use App\Mailer\MailerInterface;
use App\Repository\Contract\PasswordResetRepositoryInterface;
use App\Repository\Contract\UserRepositoryInterface;
use DateTime;
use Swift_Message;
use Twig\Environment;
use App\Service\Contract\PasswordManagerInterface;

class PasswordManager implements PasswordManagerInterface
{
    private PasswordResetRepositoryInterface $passwordResetRepository;
    private UserRepositoryInterface $userRepository;
    private MailerInterface $mailer;
    private Environment $twig;

    /**
     * PasswordManager constructor.
     *
     * @param PasswordResetRepositoryInterface $passwordResetRepository
     * @param UserRepositoryInterface $userRepository
     * @param MailerInterface $mailer
     * @param Environment $twig
     */
    public function __construct(
        PasswordResetRepositoryInterface $passwordResetRepository,
        UserRepositoryInterface $userRepository,
        MailerInterface $mailer,
        Environment $twig
    ) {
        $this->passwordResetRepository = $passwordResetRepository;
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
        $this->twig = $twig;
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
        $passwordReset = $this->passwordResetRepository->findPasswordResetByHashedResetCode($hashedResetCode);

        return $passwordReset !== null && $passwordReset->user !== null;
    }

    public function resetCodeHasExpired(string $resetCode): bool
    {
        $hashedResetCode = $this->hashCode($resetCode);
        $passwordReset = $this->passwordResetRepository->findPasswordResetByHashedResetCode($hashedResetCode);

        if ($passwordReset === null) {
            return true;
        }

        $currentTime = new DateTime();
        $resetTime = $passwordReset->reset_time ?? null;
        
        if ($resetTime === null) {
            return true;
        }

        $timeDifference = date_diff($resetTime instanceof DateTime ? $resetTime : new DateTime($resetTime), $currentTime);

        $hasExpired = $timeDifference->d > 1;

        if ($hasExpired) {
            $this->passwordResetRepository->deletePasswordResetByHashedResetCode($hashedResetCode);
        }

        return $hasExpired;
    }

    public function getPasswordResetByResetCode(string $resetCode): PasswordReset
    {
        $hashedResetCode = $this->hashCode($resetCode);

        return $this->passwordResetRepository->findPasswordResetByHashedResetCode($hashedResetCode);
    }

    public function createPasswordResetEntity(string $email): ?PasswordReset
    {
        $passwordReset = new PasswordReset();

        //Finds the user based on the email
        $user = $this->userRepository->findUserByEmail($email);

        if ($user === null) {
            return null;
        }

        //Creates a random hex-string as reset code
        $resetCode = $this->generateRandomResetCode();

        //Hashes the random reset code to store in the database
        $hashedResetCode = $this->hashCode($resetCode);

        //Adds the info in the passwordReset entity
        $passwordReset->user_id = $user->id;
        $passwordReset->reset_code = $resetCode;
        $passwordReset->hashed_reset_code = $hashedResetCode;

        return $passwordReset;
    }

    public function sendResetCode(PasswordReset $passwordReset)
    {
        //Sends a email with the url for resetting the password
        $emailMessage = (new Swift_Message())
            ->setSubject('Tilbakestill passord for vektorprogrammet.no')
            ->setFrom(array('ikkesvar@vektorprogrammet.no' => 'Vektorprogrammet'))
            ->setTo($passwordReset->user->email)
            ->setBody($this->twig->render('reset_password/new_password_email.txt.twig', array(
                'resetCode' => $passwordReset->reset_code,
                'user' => $passwordReset->user,
            )));
        $this->mailer->send($emailMessage);
    }
}

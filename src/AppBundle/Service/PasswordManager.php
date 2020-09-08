<?php


namespace AppBundle\Service;

use AppBundle\Entity\PasswordReset;
use AppBundle\Mailer\MailerInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Swift_Message;
use Twig\Environment;

class PasswordManager
{
    private $em;
    private $mailer;
    private $twig;

    /**
     * PasswordManager constructor.
     *
     * @param EntityManagerInterface $em
     * @param MailerInterface $mailer
     * @param Environment $twig
     */
    public function __construct(EntityManagerInterface $em, MailerInterface $mailer, Environment $twig)
    {
        $this->em = $em;
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
        $passwordReset = $this->em->getRepository('AppBundle:PasswordReset')->findPasswordResetByHashedResetCode($hashedResetCode);

        return $passwordReset !== null && $passwordReset->getUser() !== null;
    }

    public function resetCodeHasExpired(string $resetCode): bool
    {
        $hashedResetCode = $this->hashCode($resetCode);
        $passwordReset = $this->em->getRepository('AppBundle:PasswordReset')->findPasswordResetByHashedResetCode($hashedResetCode);

        $currentTime = new DateTime();
        $timeDifference = date_diff($passwordReset->getResetTime(), $currentTime);

        $hasExpired = $timeDifference->d > 1;

        if ($hasExpired) {
            $this->em->getRepository('AppBundle:PasswordReset')->deletePasswordResetByHashedResetCode($hashedResetCode);
        }

        return $hasExpired;
    }

    public function getPasswordResetByResetCode(string $resetCode): PasswordReset
    {
        $hashedResetCode = $this->hashCode($resetCode);

        return $this->em->getRepository('AppBundle:PasswordReset')->findPasswordResetByHashedResetCode($hashedResetCode);
    }

    public function createPasswordResetEntity(string $email)
    {
        $passwordReset = new PasswordReset();

        //Finds the user based on the email
        $user = $this->em->getRepository('AppBundle:User')->findUserByEmail($email);

        if ($user === null) {
            return null;
        }

        //Creates a random hex-string as reset code
        $resetCode = $this->generateRandomResetCode();

        //Hashes the random reset code to store in the database
        $hashedResetCode = $this->hashCode($resetCode);

        //Adds the info in the passwordReset entity
        $passwordReset->setUser($user);
        $passwordReset->setResetCode($resetCode);
        $passwordReset->setHashedResetCode($hashedResetCode);

        return $passwordReset;
    }

    public function sendResetCode(PasswordReset $passwordReset)
    {
        //Sends a email with the url for resetting the password
        $emailMessage = (new Swift_Message())
            ->setSubject('Tilbakestill passord for vektorprogrammet.no')
            ->setFrom(array('ikkesvar@vektorprogrammet.no' => 'Vektorprogrammet'))
            ->setTo($passwordReset->getUser()->getEmail())
            ->setBody($this->twig->render('reset_password/new_password_email.txt.twig', array(
                'resetCode' => $passwordReset->getResetCode(),
                'user' => $passwordReset->getUser(),
            )));
        $this->mailer->send($emailMessage);
    }
}

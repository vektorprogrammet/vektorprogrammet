<?php
/**
 * Created by IntelliJ IDEA.
 * User: kristoffer
 * Date: 03.01.17
 * Time: 17:42.
 */

namespace AppBundle\Service;

use AppBundle\Entity\PasswordReset;
use Doctrine\ORM\EntityManager;

class PasswordManager
{
    private $em;

    /**
     * PasswordManager constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
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

        $currentTime = new \DateTime();
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
}

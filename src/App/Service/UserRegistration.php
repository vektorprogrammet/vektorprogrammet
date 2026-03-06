<?php

namespace App\Service;

use App\Entity\Role;
use App\Entity\User;
use App\Mailer\MailerInterface;
use App\Role\Roles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class UserRegistration
{
    public function __construct(
        private readonly Environment $twig,
        private readonly EntityManagerInterface $em,
        private readonly MailerInterface $mailer,
    ) {
    }

    public function setNewUserCode(User $user)
    {
        $newUserCode = bin2hex(openssl_random_pseudo_bytes(16));
        $hashedNewUserCode = hash('sha512', $newUserCode, false);
        $user->setNewUserCode($hashedNewUserCode);

        $this->em->persist($user);
        $this->em->flush();

        return $newUserCode;
    }

    public function createActivationEmail(User $user, $newUserCode)
    {
        return (new Email())
            ->subject('Velkommen til Vektorprogrammet!')
            ->from(new Address('vektorprogrammet@vektorprogrammet.no', 'Vektorprogrammet'))
            ->replyTo($user->getFieldOfStudy()->getDepartment()->getEmail())
            ->to($user->getEmail())
            ->text($this->twig->render('new_user/create_new_user_email.txt.twig', [
                'newUserCode' => $newUserCode,
                'name' => $user->getFullName(),
            ]));
    }

    public function sendActivationCode(User $user)
    {
        $newUserCode = $this->setNewUserCode($user);

        $this->mailer->send($this->createActivationEmail($user, $newUserCode));
    }

    public function getHashedCode(string $newUserCode): string
    {
        return hash('sha512', $newUserCode, false);
    }

    public function activateUserByNewUserCode(string $newUserCode)
    {
        $hashedNewUserCode = $this->getHashedCode($newUserCode);
        $user = $this->em->getRepository(User::class)->findUserByNewUserCode($hashedNewUserCode);
        if ($user === null) {
            return null;
        }

        if ($user->getUserName() === null) {
            // Set default username to email
            $user->setUserName($user->getEmail());
        }

        $user->setNewUserCode(null);

        $user->setActive('1');

        if (count($user->getRoles()) === 0) {
            $role = $this->em->getRepository(Role::class)->findByRoleName(Roles::ASSISTANT);
            $user->addRole($role);
        }

        return $user;
    }
}

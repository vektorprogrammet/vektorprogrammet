<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;
use AppBundle\Mailer\MailerInterface;
use AppBundle\Role\Roles;
use Doctrine\ORM\EntityManagerInterface;
use Swift_Message;
use Twig\Environment;

class UserRegistration
{
    private $twig;
    private $em;
    private $mailer;

    /**
     * UserRegistration constructor.
     *
     * @param Environment $twig
     * @param EntityManagerInterface     $em
     * @param MailerInterface   $mailer
     */
    public function __construct(Environment $twig, EntityManagerInterface $em, MailerInterface $mailer)
    {
        $this->twig = $twig;
        $this->em = $em;
        $this->mailer = $mailer;
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
        /** @var Swift_Message $emailMessage */
        $emailMessage = (new Swift_Message())
            ->setSubject('Velkommen til Vektorprogrammet!')
            ->setFrom(array('vektorprogrammet@vektorprogrammet.no' => 'Vektorprogrammet'))
            ->setReplyTo($user->getFieldOfStudy()->getDepartment()->getEmail())
            ->setTo($user->getEmail())
            ->setBody($this->twig->render('new_user/create_new_user_email.txt.twig', array(
                'newUserCode' => $newUserCode,
                'name' => $user->getFullName(),
            )));

        return $emailMessage;
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
        $user = $this->em->getRepository('AppBundle:User')->findUserByNewUserCode($hashedNewUserCode);
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
            $role = $this->em->getRepository('AppBundle:Role')->findByRoleName(Roles::ASSISTANT);
            $user->addRole($role);
        }

        return $user;
    }
}

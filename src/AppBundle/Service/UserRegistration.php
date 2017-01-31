<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;
use AppBundle\Role\Roles;
use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class UserRegistration
{
    private $twig;
    private $em;
    private $mailer;
    private $logger;

    /**
     * UserRegistration constructor.
     *
     * @param \Twig_Environment $twig
     * @param EntityManager     $em
     * @param \Swift_Mailer     $mailer
     * @param LoggerInterface   $logger
     */
    public function __construct(\Twig_Environment $twig, EntityManager $em, \Swift_Mailer $mailer, LoggerInterface $logger)
    {
        $this->twig = $twig;
        $this->em = $em;
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    public function setNewUserCode(User $user)
    {
        $newUserCode = bin2hex(openssl_random_pseudo_bytes(16));
        $hashedNewUserCode = hash('sha512', $newUserCode, false);
        $user->setNewUserCode($hashedNewUserCode);

        return $newUserCode;
    }

    public function createActivationEmail(User $user, $newUserCode)
    {
        /** @var \Swift_Mime_Message $emailMessage */
        $emailMessage = \Swift_Message::newInstance()
            ->setSubject('Velkommen til vektorprogrammet')
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

        $this->em->persist($user);
        $this->em->flush();

        $this->mailer->send($this->createActivationEmail($user, $newUserCode));

        $this->logger->info("Activation email sent to {$user} at {$user->getEmail()}");
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

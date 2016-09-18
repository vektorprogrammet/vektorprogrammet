<?php

namespace AppBundle\UserRegistration;

use AppBundle\Entity\User;

class UserRegistration
{
    private $twig;

    /**
     * UserRegistration constructor.
     *
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
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
}

<?php

namespace App\Service;

use App\Models\Role;
use App\Models\User;
use App\Mailer\MailerInterface;
use App\Repository\Contract\RoleRepositoryInterface;
use App\Repository\Contract\UserRepositoryInterface;
use App\Role\Roles;
use Swift_Message;
use Twig\Environment;
use App\Service\Contract\UserRegistrationInterface;

class UserRegistration implements UserRegistrationInterface
{
    private Environment $twig;
    private UserRepositoryInterface $userRepository;
    private RoleRepositoryInterface $roleRepository;
    private MailerInterface $mailer;

    /**
     * UserRegistration constructor.
     *
     * @param Environment $twig
     * @param UserRepositoryInterface $userRepository
     * @param RoleRepositoryInterface $roleRepository
     * @param MailerInterface $mailer
     */
    public function __construct(
        Environment $twig,
        UserRepositoryInterface $userRepository,
        RoleRepositoryInterface $roleRepository,
        MailerInterface $mailer
    ) {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->mailer = $mailer;
    }

    public function setNewUserCode(User $user): string
    {
        $newUserCode = bin2hex(openssl_random_pseudo_bytes(16));
        $hashedNewUserCode = hash('sha512', $newUserCode, false);
        $user->new_user_code = $hashedNewUserCode;

        $user->save();

        return $newUserCode;
    }

    public function createActivationEmail(User $user, string $newUserCode): Swift_Message
    {
        return (new Swift_Message())
            ->setSubject('Velkommen til Vektorprogrammet!')
            ->setFrom(array('vektorprogrammet@vektorprogrammet.no' => 'Vektorprogrammet'))
            ->setReplyTo($user->fieldOfStudy->department->email ?? 'ikkesvar@vektorprogrammet.no')
            ->setTo($user->email)
            ->setBody($this->twig->render('new_user/create_new_user_email.txt.twig', array(
                'newUserCode' => $newUserCode,
                'name' => $user->getFullName(),
            )));
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

    public function activateUserByNewUserCode(string $newUserCode): ?User
    {
        $hashedNewUserCode = $this->getHashedCode($newUserCode);
        $user = $this->userRepository->findUserByNewUserCode($hashedNewUserCode);
        if ($user === null) {
            return null;
        }

        if ($user->user_name === null) {
            // Set default username to email
            $user->user_name = $user->email;
        }

        $user->new_user_code = null;
        $user->is_active = true;

        $userRoles = $user->roles;
        if ($userRoles === null || $userRoles->isEmpty()) {
            $role = $this->roleRepository->findByRoleName(Roles::ASSISTANT);
            $user->roles()->sync([$role->id]);
        }

        $user->save();

        return $user;
    }
}

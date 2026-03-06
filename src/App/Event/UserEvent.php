<?php

namespace App\Event;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class UserEvent extends Event
{
    public const CREATED = 'user.created';
    public const EDITED = 'user.edited';
    public const DELETED = 'user.deleted';
    public const COMPANY_EMAIL_EDITED = 'user.company_email_edited';

    /**
     * @param string $oldEmail
     */
    public function __construct(private readonly User $user, private $oldEmail)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getOldEmail()
    {
        return $this->oldEmail;
    }
}

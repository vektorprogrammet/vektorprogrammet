<?php

namespace AppBundle\Event;

use AppBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class UserEvent extends Event implements CrudEvent
{
    const CREATED = 'user.created';
    const EDITED = 'user.edited';
    const DELETED = 'user.deleted';
    const COMPANY_EMAIL_EDITED = 'user.company_email_edited';

    private $user;
    private $oldEmail;

    /**
     * @param User $user
     * @param string $oldEmail
     */
    public function __construct(User $user, $oldEmail)
    {
        $this->user = $user;
        $this->oldEmail = $oldEmail;
    }

    /**
     * @return User
     */
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

    public function getObject()
    {
        return $this->getUser();
    }

    public static function created(): string
    {
        return self::CREATED;
    }

    public static function updated(): string
    {
        return self::EDITED;
    }

    public static function deleted(): string
    {
        return self::DELETED;
    }
}

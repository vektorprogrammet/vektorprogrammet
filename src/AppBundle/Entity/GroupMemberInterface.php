<?php

namespace AppBundle\Entity;

interface GroupMemberInterface
{

    /**
     * @return User
     */
    public function getUser(): User;

    /**
     * @return string | null
     */
    public function getPositionName();
}

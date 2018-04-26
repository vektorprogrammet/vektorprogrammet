<?php

namespace AppBundle\Entity;

interface TeamMembershipInterface
{

    /**
     * @return User
     */
    public function getUser(): User;

    /**
     * @return string | null
     */
    public function getPositionName();

    /**
     * @return TeamInterface
     */
    public function getTeam();
}

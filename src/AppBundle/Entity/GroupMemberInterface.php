<?php

namespace AppBundle\Entity;

interface GroupMemberInterface
{
    public function getUser(): User;
    public function getPositionName(): string;
}

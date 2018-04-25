<?php

namespace AppBundle\Entity;

interface TeamInterface
{
    public function getName();
    public function getEmail();
    public function getShortDescription();
    public function getDescription();
    public function getAcceptApplication();
    public function getTeamMemberships();
    public function getActiveTeamMemberships();
}

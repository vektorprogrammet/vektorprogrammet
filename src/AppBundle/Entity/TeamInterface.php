<?php

namespace AppBundle\Entity;

interface TeamInterface
{

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @param string $email
     */
    public function setEmail($email);

    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getShortDescription();

    /**
     * @param string $shortDescription
     */
    public function setShortDescription($shortDescription);

    /**
     * @return bool
     */
    public function getAcceptApplication();

    /**
     * @return TeamMembershipInterface
     */
    public function getTeamMemberships();

    /**
     * @return TeamMembershipInterface
     */
    public function getActiveTeamMemberships();

    /**
     * @return User
     */
    public function getActiveUsers();
}

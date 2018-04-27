<?php

namespace AppBundle\Entity;

interface TeamMembershipInterface
{

    /**
     * @return \AppBundle\Entity\User
     */
    public function getUser(): User;

    /**
     * @return string | null
     */
    public function getPositionName();

    /**
     * @return \AppBundle\Entity\TeamInterface
     */
    public function getTeam();

    /**
     * @return \AppBundle\Entity\Semester
     */
    public function getStartSemester();

    /**
     * @param \AppBundle\Entity\Semester $semester
     *
     * @return \AppBundle\Entity\TeamMembershipInterface
     */
    public function setStartSemester(Semester $semester = null);

    /**
     * @return \AppBundle\Entity\Semester
     */
    public function getEndSemester();

    /**
     * @param \AppBundle\Entity\Semester $semester
     *
     * @return \AppBundle\Entity\TeamMembershipInterface
     */
    public function setEndSemester(Semester $semester = null);

    /**
     * @return bool
     */
    public function isActive();
}

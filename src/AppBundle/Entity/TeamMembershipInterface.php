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

    /**
     * @return Semester
     */
    public function getStartSemester();

    /**
     * @param Semester $semester
     *
     * @return TeamMembershipInterface
     */
    public function setStartSemester(Semester $semester = null);

    /**
     * @return Semester
     */
    public function getEndSemester();

    /**
     * @param Semester $semester
     *
     * @return TeamMembershipInterface
     */
    public function setEndSemester(Semester $semester = null);

    /**
     * @return bool
     */
    public function isActive();
}

<?php

declare(strict_types=1);

namespace App\Shared\Contracts;

use App\Identity\Infrastructure\Entity\User;
use App\Shared\Entity\Semester;

interface TeamMembershipInterface
{
    public function getUser(): User;

    /**
     * @return string|null
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
     * @return TeamMembershipInterface
     */
    public function setStartSemester(?Semester $semester = null);

    /**
     * @return Semester|null
     */
    public function getEndSemester();

    /**
     * @return TeamMembershipInterface
     */
    public function setEndSemester(?Semester $semester = null);

    /**
     * @return bool
     */
    public function isActive();
}

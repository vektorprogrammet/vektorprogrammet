<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="team_membership")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\TeamMembershipRepository")
 */
class TeamMembership implements TeamMembershipInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="teamMemberships")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @Assert\Valid
     * @Assert\NotNull(message="Dette feltet kan ikke være tomt")
     **/
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Semester")
     * @Assert\Valid
     * @Assert\NotNull(message="Dette feltet kan ikke være tomt")
     */
    protected $startSemester;

    /**
     * @ORM\ManyToOne(targetEntity="Semester")
     * @Assert\Valid
     */
    protected $endSemester;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $deletedTeamName;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     * @Assert\NotNull(message="Dette feltet kan ikke være tomt")
     */
    private $isTeamLeader;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     * @Assert\NotNull(message="Dette feltet kan ikke være tomt")
     */
    private $isSuspended;

    /**
     * @var Team
     *
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="teamMemberships")
     * @ORM\JoinColumn(onDelete="SET NULL")
     **/
    protected $team;

    /**
     * @var Position
     *
     * @ORM\ManyToOne(targetEntity="Position")
     * @ORM\JoinColumn(name="position_id", referencedColumnName="id", onDelete="SET NULL")
     * @Assert\Valid
     * @Assert\NotNull(message="Dette feltet kan ikke være tomt")
     **/
    protected $position;

    public function __construct()
    {
        $this->isTeamLeader = false;
        $this->isSuspended = false;
    }

    public function __toString()
    {
        return (string) $this->getId();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user.
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return TeamMembership
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Set team.
     *
     * @param \AppBundle\Entity\Team $team
     *
     * @return TeamMembership
     */
    public function setTeam(\AppBundle\Entity\Team $team = null)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get team.
     *
     * @return \AppBundle\Entity\Team
     */
    public function getTeam()
    {
        return $this->team;
    }


    /**
     * Set position.
     *
     * @param \AppBundle\Entity\Position $position
     *
     * @return TeamMembership
     */
    public function setPosition(\AppBundle\Entity\Position $position = null)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position.
     *
     * @return Position
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set startSemester.
     *
     * @param \AppBundle\Entity\Semester $startSemester
     *
     * @return TeamMembership
     */
    public function setStartSemester(Semester $startSemester = null)
    {
        $this->startSemester = $startSemester;

        return $this;
    }

    /**
     * Get startSemester.
     *
     * @return \AppBundle\Entity\Semester
     */
    public function getStartSemester()
    {
        return $this->startSemester;
    }

    /**
     * Set endSemester.
     *
     * @param \AppBundle\Entity\Semester $endSemester
     *
     * @return TeamMembership
     */
    public function setEndSemester(Semester $endSemester = null)
    {
        $this->endSemester = $endSemester;

        return $this;
    }

    /**
     * Get endSemester.
     *
     * @return \AppBundle\Entity\Semester
     */
    public function getEndSemester()
    {
        return $this->endSemester;
    }

    /**
     * @param Semester $semester
     *
     * @return bool
     */
    public function isActiveInSemester(Semester $semester)
    {
        $semesterStartLaterThanTeamMembership = $semester->getStartDate() >= $this->getStartSemester()->getStartDate();
        $semesterEndsBeforeTeamMembership = $this->getEndSemester() === null || $semester->getEndDate() <= $this->getEndSemester()->getEndDate();

        return $semesterStartLaterThanTeamMembership && $semesterEndsBeforeTeamMembership;
    }


    public function isActive()
    {
        $department = $this->team->getDepartment();
        $activeSemester = $department->getCurrentOrLatestAdmissionPeriod()->getSemester();

        return $this->isActiveInSemester($activeSemester);
    }


    /**
     * @return string
     */
    public function getTeamName(): string
    {
        if ($this->deletedTeamName !== null) {
            return $this->deletedTeamName;
        }

        return $this->team->getName();
    }

    /**
     * @param string $deletedTeamName
     */
    public function setDeletedTeamName(string $deletedTeamName)
    {
        $this->deletedTeamName = $deletedTeamName;
    }

    /**
     * @return string
     */
    public function getPositionName(): string
    {
        return $this->position->getName();
    }

    /**
     * @return bool
     */
    public function isTeamLeader(): bool
    {
        return $this->isTeamLeader;
    }

    /**
     * @param bool $isTeamLeader
     */
    public function setIsTeamLeader($isTeamLeader)
    {
        $this->isTeamLeader = $isTeamLeader;
    }

    /**
     * @return bool
     */
    public function isSuspended(): bool
    {
        return $this->isSuspended;
    }

    /**
     * @param bool $isSuspended
     */
    public function setIsSuspended($isSuspended)
    {
        $this->isSuspended = $isSuspended;
    }
}

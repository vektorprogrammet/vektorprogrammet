<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="executive_board_membership")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\ExecutiveBoardMembershipRepository")
 */
class ExecutiveBoardMembership implements TeamMembershipInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="executiveBoardMemberships")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\Valid
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     **/
    private $user;

    /**
     * @var ExecutiveBoard
     * @ORM\ManyToOne(targetEntity="ExecutiveBoard", inversedBy="boardMemberships")
     **/
    private $board;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Valid
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     **/
    private $positionName;

    /**
     * @var Semester
     * @ORM\ManyToOne(targetEntity="Semester")
     * @Assert\Valid
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     */
    protected $startSemester;

    /**
     * @var Semester
     * @ORM\ManyToOne(targetEntity="Semester")
     * @Assert\Valid
     */
    protected $endSemester;

    /**
     * ExecutiveBoardMembership constructor.
     */
    public function __construct()
    {
        $this->positionName = '';
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
     * @param User $user
     *
     * @return ExecutiveBoardMembership
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Set board.
     *
     * @param ExecutiveBoard $board
     *
     * @return ExecutiveBoardMembership
     */
    public function setBoard(ExecutiveBoard $board = null)
    {
        $this->board = $board;

        return $this;
    }

    /**
     * Get board.
     *
     * @return ExecutiveBoard
     */
    public function getBoard()
    {
        return $this->board;
    }

    /**
     * @return string | null
     */
    public function getPositionName()
    {
        return $this->positionName;
    }

    /**
     * @param string $positionName
     *
     * @return ExecutiveBoardMembership $this
     */
    public function setPositionName($positionName)
    {
        $this->positionName = $positionName;
        return $this;
    }

    /**
     * @param \AppBundle\Entity\Semester $semester
     *
     * @return \AppBundle\Entity\ExecutiveBoardMembership
     */
    public function setStartSemester(Semester $semester = null)
    {
        $this->startSemester = $semester;
        return $this;
    }

    /**
     * @return Semester
     */
    public function getStartSemester()
    {
        return $this->startSemester;
    }

    /**
     * @param \AppBundle\Entity\Semester $semester
     *
     * @return \AppBundle\Entity\ExecutiveBoardMembership
     */
    public function setEndSemester(Semester $semester = null)
    {
        $this->endSemester = $semester;
        return $this;
    }

    /**
     * @return \AppBundle\Entity\Semester | null
     */
    public function getEndSemester()
    {
        return $this->endSemester;
    }

    public function isActive()
    {
        $now = new DateTime();
        $termEndsInFuture = $this->endSemester === null || $this->endSemester->getEndDate() > $now;
        $termStartedInPast = $this->startSemester !== null && $this->startSemester->getStartDate() < $now;
        return $termEndsInFuture && $termStartedInPast;
    }

    /**
     * @return \AppBundle\Entity\TeamInterface
     */
    public function getTeam()
    {
        return $this->board;
    }
}

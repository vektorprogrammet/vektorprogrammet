<?php

namespace AppBundle\Entity;

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
     * @Assert\Valid
     **/
    private $user;

    /**
     * @var ExecutiveBoard
     * @ORM\ManyToOne(targetEntity="ExecutiveBoard", inversedBy="members")
     **/
    private $board;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Valid
     *
     **/
    private $position;

    /**
     * @var Semester
     * @ORM\ManyToOne(targetEntity="Semester")
     * @Assert\Valid
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
        $this->position = '';
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
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param string $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return string | null
     */
    public function getPositionName()
    {
        return $this->position;
    }

    /**
     * @param Semester $startSemester
     *
     * @return ExecutiveBoardMembership
     */
    public function setStartSemester($startSemester)
    {
        $this->startSemester = $startSemester;
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
     * @param Semester $endSemester
     *
     * @return ExecutiveBoardMembership
     */
    public function setEndSemester($endSemester)
    {
        $this->endSemester = $endSemester;
        return $this;
    }

    /**
     * @return Semester | null
     */
    public function getEndSemester()
    {
        return $this->endSemester;
    }

    public function isActive()
    {
        $now = new \DateTime();
        $termEndsInFuture = $this->getEndSemester() === null || $this->getEndSemester()->getSemesterEndDate() > $now;
        $termStartedInPast = $this->getStartSemester() !== null && $this->getStartSemester()->getSemesterStartDate() < $now;
        return $termEndsInFuture && $termStartedInPast;
    }
}

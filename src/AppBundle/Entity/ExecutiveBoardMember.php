<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="executive_board_member")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\ExecutiveBoardMemberRepository")
 */
class ExecutiveBoardMember implements GroupMemberInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @Assert\Valid
     **/
    private $user;

    /**
     * @var ExecutiveBoard
     * @ORM\ManyToOne(targetEntity="ExecutiveBoard", inversedBy="users")
     **/
    private $board;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Valid
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
     * ExecutiveBoardMember constructor.
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
     * @return ExecutiveBoardMember
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
     * @return ExecutiveBoardMember
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
     * @return string
     */
    public function getPosition(): string
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

    public function getPositionName(): string
    {
        return $this->position;
    }

    /**
     * @param Semester $startSemester
     *
     * @return ExecutiveBoardMember
     */
    public function setStartSemester($startSemester) {
        $this->startSemester = $startSemester;
        return $this;
    }

    /**
     * @return Semester
     */
    public function getStartSemester() {
        return $this->startSemester;
    }

    /**
     * @param Semester $endSemester
     *
     * @return ExecutiveBoardMember
     */
    public function setEndSemester($endSemester) {
        $this->endSemester = $endSemester;
        return $this;
    }

    /**
     * @return Semester | null
     */
    public function getEndSemester() {
        return $this->endSemester;
    }

    public function isActive() {
        $now = new \DateTime();
        $termEndsInFuture = $this->getEndSemester() === null || $this->getEndSemester()->getSemesterEndDate() > $now;
        $termStartedInPast = $this->getStartSemester() !== null && $this->getStartSemester()->getSemesterStartDate() < $now;
        return $termEndsInFuture && $termStartedInPast;
    }
}

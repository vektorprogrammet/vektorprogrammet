<?php

namespace App\Organization\Infrastructure\Entity;

use App\Identity\Infrastructure\Entity\User;
use App\Organization\Infrastructure\Repository\ExecutiveBoardMembershipRepository;
use App\Shared\Contracts\TeamMembershipInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Shared\Entity\Semester;

#[ORM\Table(name: 'executive_board_membership')]
#[ORM\Entity(repositoryClass: ExecutiveBoardMembershipRepository::class)]
class ExecutiveBoardMembership implements TeamMembershipInterface, \Stringable
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private $id;
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'executiveBoardMemberships')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[Assert\Valid]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    private $user;

    /**
     * @var ExecutiveBoard
     */
    #[ORM\ManyToOne(targetEntity: ExecutiveBoard::class, inversedBy: 'boardMemberships')]
    private $board;
    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Valid]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    private $positionName;

    /**
     * @var Semester
     */
    #[ORM\ManyToOne(targetEntity: Semester::class)]
    #[Assert\Valid]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    protected $startSemester;

    /**
     * @var Semester
     */
    #[ORM\ManyToOne(targetEntity: Semester::class)]
    #[Assert\Valid]
    protected $endSemester;

    /**
     * ExecutiveBoardMembership constructor.
     */
    public function __construct()
    {
        $this->positionName = '';
    }

    public function __toString(): string
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
     * @return ExecutiveBoardMembership
     */
    public function setUser(?User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Set board.
     *
     * @return ExecutiveBoardMembership
     */
    public function setBoard(?ExecutiveBoard $board = null)
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
     * @return string|null
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
     * @return ExecutiveBoardMembership
     */
    public function setStartSemester(?Semester $semester = null)
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
     * @return ExecutiveBoardMembership
     */
    public function setEndSemester(?Semester $semester = null)
    {
        $this->endSemester = $semester;

        return $this;
    }

    /**
     * @return Semester|null
     */
    public function getEndSemester()
    {
        return $this->endSemester;
    }

    public function isActive()
    {
        $now = new \DateTime();
        $termEndsInFuture = $this->endSemester === null || $this->endSemester->getEndDate() > $now;
        $termStartedInPast = $this->startSemester !== null && $this->startSemester->getStartDate() < $now;

        return $termEndsInFuture && $termStartedInPast;
    }

    /**
     * @return TeamInterface
     */
    public function getTeam()
    {
        return $this->board;
    }
}

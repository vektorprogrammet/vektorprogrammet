<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Repository\TeamMembershipRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'team_membership')]
#[ORM\Entity(repositoryClass: TeamMembershipRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
    ],
    normalizationContext: ['groups' => ['team_member:read']],
)]
class TeamMembership implements TeamMembershipInterface, \Stringable
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[Groups(['team_member:read', 'team:detail'])]
    protected $id;
    #[ORM\ManyToOne(targetEntity: 'User', inversedBy: 'teamMemberships')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[Assert\Valid]
    #[Assert\NotNull(message: 'Dette feltet kan ikke være tomt')]
    #[Groups(['team_member:read', 'team:detail'])]
    protected $user;

    #[ORM\ManyToOne(targetEntity: 'Semester')]
    #[Assert\Valid]
    #[Assert\NotNull(message: 'Dette feltet kan ikke være tomt')]
    protected $startSemester;

    #[ORM\ManyToOne(targetEntity: 'Semester')]
    #[Assert\Valid]
    protected $endSemester;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private $deletedTeamName;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean')]
    #[Assert\NotNull(message: 'Dette feltet kan ikke være tomt')]
    #[Groups(['team_member:read', 'team:detail'])]
    private $isTeamLeader;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean')]
    #[Assert\NotNull(message: 'Dette feltet kan ikke være tomt')]
    private $isSuspended;

    /**
     * @var Team
     */
    #[ORM\ManyToOne(targetEntity: 'Team', inversedBy: 'teamMemberships')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    protected $team;

    /**
     * @var Position
     */
    #[ORM\ManyToOne(targetEntity: 'Position')]
    #[ORM\JoinColumn(name: 'position_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    #[Assert\Valid]
    #[Assert\NotNull(message: 'Dette feltet kan ikke være tomt')]
    #[Groups(['team_member:read', 'team:detail'])]
    protected $position;

    public function __construct()
    {
        $this->isTeamLeader = false;
        $this->isSuspended = false;
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
     * @return TeamMembership
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
     * Set team.
     *
     * @return TeamMembership
     */
    public function setTeam(?Team $team = null)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get team.
     *
     * @return Team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set position.
     *
     * @return TeamMembership
     */
    public function setPosition(?Position $position = null)
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
     * @return TeamMembership
     */
    public function setStartSemester(?Semester $startSemester = null)
    {
        $this->startSemester = $startSemester;

        return $this;
    }

    /**
     * Get startSemester.
     *
     * @return Semester
     */
    public function getStartSemester()
    {
        return $this->startSemester;
    }

    /**
     * Set endSemester.
     *
     * @return TeamMembership
     */
    public function setEndSemester(?Semester $endSemester = null)
    {
        $this->endSemester = $endSemester;

        return $this;
    }

    /**
     * Get endSemester.
     *
     * @return Semester
     */
    public function getEndSemester()
    {
        return $this->endSemester;
    }

    /**
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

    public function getTeamName(): string
    {
        if ($this->deletedTeamName !== null) {
            return $this->deletedTeamName;
        }

        return $this->team->getName();
    }

    public function setDeletedTeamName(string $deletedTeamName)
    {
        $this->deletedTeamName = $deletedTeamName;
    }

    public function getPositionName(): string
    {
        return $this->position->getName();
    }

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

<?php

namespace App\Organization\Infrastructure\Entity;

use App\Admission\Infrastructure\Entity\Application;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Organization\Infrastructure\Repository\TeamRepository;
use App\Shared\Contracts\DepartmentSemesterInterface;
use App\Shared\Contracts\TeamInterface;
use App\Identity\Infrastructure\Validator as CustomAssert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'team')]
#[ORM\Entity(repositoryClass: TeamRepository::class)]
#[UniqueEntity(fields: ['department', 'name'], message: 'Et team med dette navnet finnes allerede i avdelingen.', )]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(normalizationContext: ['groups' => ['team:read', 'team:detail']]),
    ],
    normalizationContext: ['groups' => ['team:read']],
)]
class Team implements TeamInterface, \Stringable
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[Groups(['team:read', 'department:detail'])]
    protected $id;

    #[ORM\Column(type: 'string', length: 250)]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    #[Groups(['team:read', 'department:detail'])]
    protected $name;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\Email(message: 'Ugyldig e-post')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være blankt.')]
    #[CustomAssert\UniqueCompanyEmail]
    #[CustomAssert\VektorEmail]
    #[Groups(['team:read', 'department:detail'])]
    private $email;
    #[ORM\ManyToOne(targetEntity: Department::class, inversedBy: 'teams')]
    #[Assert\NotNull(message: 'Avdeling kan ikke være null')]
    protected $department;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['team:detail'])]
    private $description;

    #[ORM\Column(type: 'string', nullable: true, name: 'short_description')]
    #[Assert\Length(maxMessage: 'Maks 125 Tegn', max: '125')]
    #[Groups(['team:read', 'department:detail'])]
    private $shortDescription;

    #[ORM\Column(type: 'boolean', nullable: true)]
    #[Groups(['team:detail'])]
    private $acceptApplication;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(['team:detail'])]
    private $deadline;

    /**
     * Applications with team interest.
     *
     * @var Application[]
     */
    #[ORM\ManyToMany(targetEntity: Application::class, mappedBy: 'potentialTeams')]
    private $potentialMembers;

    /**
     * TeamInterest entities not corresponding to any Application.
     *
     * @var TeamInterest[]
     */
    #[ORM\ManyToMany(targetEntity: TeamInterest::class, mappedBy: 'potentialTeams')]
    private $potentialApplicants;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    #[Groups(['team:read', 'department:detail'])]
    private $active;

    #[ORM\OneToMany(targetEntity: TeamApplication::class, mappedBy: 'team')]
    private $applications;

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active)
    {
        $this->active = $active;
    }

    /**
     * @var TeamMembership[]
     */
    #[ORM\OneToMany(targetEntity: TeamMembership::class, mappedBy: 'team')]
    #[Groups(['team:detail'])]
    private $teamMemberships;

    /**
     * @return bool
     */
    public function getAcceptApplication()
    {
        return $this->acceptApplication;
    }

    /**
     * @return Team
     */
    public function setAcceptApplication(bool $acceptApplication)
    {
        $this->acceptApplication = $acceptApplication;

        return $this;
    }

    public function __construct()
    {
        $this->active = true;
        $this->teamMemberships = [];
    }

    public function __toString(): string
    {
        return (string) $this->getName();
    }

    public function getType()
    {
        return 'team';
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
     * Set name.
     *
     * @param string $name
     *
     * @return Team
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set department.
     *
     * @return Team
     */
    public function setDepartment(?Department $department = null)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department.
     *
     * @return Department
     */
    public function getDepartment()
    {
        return $this->department;
    }

    // Used for unit testing
    public function fromArray($data = [])
    {
        foreach ($data as $property => $value) {
            $method = "set{$property}";
            $this->$method($value);
        }
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return $this|Team
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDeadline()
    {
        return $this->deadline;
    }

    /**
     * @param \DateTime $deadline
     *
     * @return Team
     */
    public function setDeadline($deadline)
    {
        $now = new \DateTime();
        if ($this->acceptApplication && $now <= $deadline) {
            $this->deadline = $deadline;
        } else {
            $this->deadline = null;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Team
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * @param string $shortDescription
     *
     * @return Team
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * @return TeamMembership[]
     */
    public function getTeamMemberships()
    {
        return $this->teamMemberships;
    }

    /**
     * @return TeamMembership[]
     */
    public function getActiveTeamMemberships()
    {
        $histories = [];

        foreach ($this->teamMemberships as $wh) {
            $semester = $wh->getUser()->getDepartment()->getCurrentOrLatestAdmissionPeriod()->getSemester();
            if ($semester !== null && $wh->isActiveInSemester($semester)) {
                $histories[] = $wh;
            }
        }

        return $histories;
    }

    /**
     * @return User[]
     */
    public function getActiveUsers()
    {
        $activeUsers = [];

        foreach ($this->getActiveTeamMemberships() as $activeTeamMembership) {
            if (!in_array($activeTeamMembership->getUser(), $activeUsers)) {
                $activeUsers[] = $activeTeamMembership->getUser();
            }
        }

        return $activeUsers;
    }

    /**
     * @return Application[]
     */
    public function getPotentialMembers()
    {
        return $this->potentialMembers;
    }

    /**
     * @param Application[] $potentialMembers
     */
    public function setPotentialMembers($potentialMembers)
    {
        $this->potentialMembers = $potentialMembers;
    }

    /**
     * @return TeamInterest[]
     */
    public function getPotentialApplicants()
    {
        return $this->potentialApplicants;
    }

    /**
     * @param TeamInterest[] $potentialApplicants
     *
     * @return Team
     */
    public function setPotentialApplicants($potentialApplicants)
    {
        $this->potentialApplicants = $potentialApplicants;

        return $this;
    }

    public function getNumberOfPotentialMembersAndApplicantsInSemester($semester)
    {
        $array = array_merge($this->potentialApplicants->toArray(), $this->potentialMembers->toArray());
        $array = array_filter($array, fn (DepartmentSemesterInterface $a) => $a->getSemester() === $semester);

        return count($array);
    }

    /**
     * @return TeamApplication[]
     */
    public function getApplications()
    {
        return $this->applications;
    }

    public function setApplications(TeamApplication $applications): void
    {
        $this->applications = $applications;
    }

    /**
     * @return bool
     */
    public function getAcceptApplicationAndDeadline()
    {
        $now = new \DateTime();

        return ($this->acceptApplication && $now < $this->deadline) || ($this->acceptApplication && $this->deadline === null);
    }
}

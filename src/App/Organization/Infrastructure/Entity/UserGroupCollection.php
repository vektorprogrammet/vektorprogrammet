<?php

namespace App\Organization\Infrastructure\Entity;

use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Shared\Entity\Semester;

#[ORM\Entity]
#[ORM\Table(name: 'user_group_collection')]
class UserGroupCollection implements \Stringable
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private $id;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    private $name;

    /**
     * @var int
     */
    #[ORM\Column(name: 'number_of_user_groups', type: 'integer', nullable: false)]
    #[Assert\GreaterThan(value: 0)]
    private $numberUserGroups;

    /**
     * @var ArrayCollection
     */
    #[ORM\OneToMany(targetEntity: UserGroup::class, mappedBy: 'userGroupCollection', cascade: ['remove'])]
    private $userGroups;

    /**
     * @var ArrayCollection
     */
    #[ORM\ManyToMany(targetEntity: Team::class)]
    private $teams;

    /**
     * @var ArrayCollection
     */
    #[ORM\ManyToMany(targetEntity: Semester::class)]
    private $semesters;

    /**
     * @var ArrayCollection
     */
    #[ORM\ManyToMany(targetEntity: User::class)]
    private $users;

    /**
     * @var ArrayCollection
     */
    #[ORM\ManyToMany(targetEntity: Department::class)]
    private $assistantsDepartments;

    /**
     * @var array
     */
    #[ORM\Column(name: 'assistant_bolk', type: 'array')]
    private $assistantBolks;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean')]
    private $deletable;

    public function __construct()
    {
        $this->userGroups = [];
        $this->name = '';
        $this->teams = [];
        $this->semesters = [];
        $this->assistantsDepartments = [];
        $this->assistantBolks = [];
        $this->numberUserGroups = 2;
        $this->deletable = true;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @param UserGroup[] $userGroups
     */
    public function setUserGroups(array $userGroups): void
    {
        $this->userGroups = $userGroups;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return UserGroup[]
     */
    public function getUserGroups()
    {
        return $this->userGroups;
    }

    /**
     * @return Team[]
     */
    public function getTeams()
    {
        return $this->teams;
    }

    /**
     * @return Semester[]
     */
    public function getSemesters()
    {
        return $this->semesters;
    }

    /**
     * @return Department[]
     */
    public function getAssistantsDepartments()
    {
        return $this->assistantsDepartments;
    }

    /**
     * @return array
     */
    public function getAssistantBolks()
    {
        return $this->assistantBolks;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param Team[] $teams
     */
    public function setTeams(array $teams): void
    {
        $this->teams = $teams;
    }

    /**
     * @param Semester[] $semesters
     */
    public function setSemesters(array $semesters): void
    {
        $this->semesters = $semesters;
    }

    /**
     * @param Department[] $assistantsDepartments
     */
    public function setAssistantsDepartments(array $assistantsDepartments): void
    {
        $this->assistantsDepartments = $assistantsDepartments;
    }

    /**
     * @param array $assistantBolks
     */
    public function setAssistantBolks($assistantBolks)
    {
        $this->assistantBolks = $assistantBolks;
    }

    public function getNumberUserGroups(): int
    {
        return $this->numberUserGroups;
    }

    public function setNumberUserGroups(int $numberUserGroups): void
    {
        $this->numberUserGroups = $numberUserGroups;
    }

    public function getNumberTotalUsers(): ?int
    {
        $numberUsers = 0;
        foreach ($this->getUserGroups() as $userGroup) {
            $numberUsers += count($userGroup->getUsers());
        }

        return $numberUsers;
    }

    public function setNumberTotalUsers(int $numberTotalUsers): void
    {
        $this->numberTotalUsers = $numberTotalUsers;
    }

    public function isDeletable(): bool
    {
        return $this->deletable;
    }

    public function setDeletable(bool $deletable): void
    {
        $this->deletable = $deletable;
    }

    /**
     * @return ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }

    public function setUsers(ArrayCollection $users): void
    {
        $this->users = $users;
    }
}

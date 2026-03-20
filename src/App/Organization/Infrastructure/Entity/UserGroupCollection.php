<?php

declare(strict_types=1);

namespace App\Organization\Infrastructure\Entity;

use App\Identity\Infrastructure\Entity\User;
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
     * @var ArrayCollection<int, UserGroup>
     */
    #[ORM\OneToMany(targetEntity: UserGroup::class, mappedBy: 'userGroupCollection', cascade: ['remove'])]
    private ArrayCollection $userGroups;

    /**
     * @var ArrayCollection<int, Team>
     */
    #[ORM\ManyToMany(targetEntity: Team::class)]
    private ArrayCollection $teams;

    /**
     * @var ArrayCollection<int, Semester>
     */
    #[ORM\ManyToMany(targetEntity: Semester::class)]
    private ArrayCollection $semesters;

    /**
     * @var ArrayCollection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class)]
    private ArrayCollection $users;

    /**
     * @var ArrayCollection<int, Department>
     */
    #[ORM\ManyToMany(targetEntity: Department::class)]
    private ArrayCollection $assistantsDepartments;

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
        $this->userGroups = new ArrayCollection();
        $this->name = '';
        $this->teams = new ArrayCollection();
        $this->semesters = new ArrayCollection();
        $this->assistantsDepartments = new ArrayCollection();
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
        $this->userGroups = new ArrayCollection($userGroups);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ArrayCollection<int, UserGroup>
     */
    public function getUserGroups(): ArrayCollection
    {
        return $this->userGroups;
    }

    /**
     * @return ArrayCollection<int, Team>
     */
    public function getTeams(): ArrayCollection
    {
        return $this->teams;
    }

    /**
     * @return ArrayCollection<int, Semester>
     */
    public function getSemesters(): ArrayCollection
    {
        return $this->semesters;
    }

    /**
     * @return ArrayCollection<int, Department>
     */
    public function getAssistantsDepartments(): ArrayCollection
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
        $this->teams = new ArrayCollection($teams);
    }

    /**
     * @param Semester[] $semesters
     */
    public function setSemesters(array $semesters): void
    {
        $this->semesters = new ArrayCollection($semesters);
    }

    /**
     * @param Department[] $assistantsDepartments
     */
    public function setAssistantsDepartments(array $assistantsDepartments): void
    {
        $this->assistantsDepartments = new ArrayCollection($assistantsDepartments);
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

    public function isDeletable(): bool
    {
        return $this->deletable;
    }

    public function setDeletable(bool $deletable): void
    {
        $this->deletable = $deletable;
    }

    /**
     * @return ArrayCollection<int, User>
     */
    public function getUsers(): ArrayCollection
    {
        return $this->users;
    }

    /**
     * @param ArrayCollection<int, User> $users
     */
    public function setUsers(ArrayCollection $users): void
    {
        $this->users = $users;
    }
}

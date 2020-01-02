<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="user_group_collection")
 * @ORM\Entity
 *
 */
class UserGroupCollection
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     */
    private $name;


    /**
     * @var int
     * @ORM\Column(name="number_of_user_groups", type="integer", nullable = false)
     * @Assert\GreaterThan(
     *     value = 0
     * )
     */
    private $numberUserGroups;



    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="UserGroup", mappedBy="userGroupCollection", cascade={"remove"})
     */
    private $userGroups;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Team")
     */
    private $teams;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Semester")
     */
    private $semesters;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="User")
     */
    private $users;


    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Department")
     */
    private $assistantsDepartments;


    /**
     * @var array
     * @ORM\Column(name="assistant_bolk", type="array")
     */
    private $assistantBolks;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $deletable;


    public function __construct()
    {
        $this->userGroups = array();
        $this->name = "";
        $this->teams = array();
        $this->semesters = array();
        $this->assistantsDepartments = array();
        $this->assistantBolks = array();
        $this->numberUserGroups = 2;
        $this->deletable = true;
    }

    public function __toString()
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


    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
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

    /**
     * @return int
     */
    public function getNumberUserGroups(): int
    {
        return $this->numberUserGroups;
    }

    /**
     * @param int $numberUserGroups
     */
    public function setNumberUserGroups(int $numberUserGroups): void
    {
        $this->numberUserGroups = $numberUserGroups;
    }


    /**
     * @return int
     */
    public function getNumberTotalUsers(): ?int
    {
        $numberUsers = 0;
        foreach ($this->getUserGroups() as $userGroup) {
            $numberUsers += count($userGroup->getUsers());
        }
        return $numberUsers;
    }

    /**
     * @param int $numberTotalUsers
     */
    public function setNumberTotalUsers(int $numberTotalUsers): void
    {
        $this->numberTotalUsers = $numberTotalUsers;
    }

    /**
     * @return bool
     */
    public function isDeletable(): bool
    {
        return $this->deletable;
    }

    /**
     * @param bool $deletable
     */
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

    /**
     * @param ArrayCollection $users
     */
    public function setUsers(ArrayCollection $users): void
    {
        $this->users = $users;
    }
}

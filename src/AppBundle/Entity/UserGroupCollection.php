<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="usergroup_collection")
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
     * @ORM\Column(type="integer", nullable = false)
     */
    private $numberUserGroups;


    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="UserGroup", mappedBy="userGroupCollection")
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
     * @ORM\ManyToMany(targetEntity="Department")
     */
    private $assistantsDepartments;


    /**
     * @var array
     * @ORM\Column(type="array")
     */
    private $assistantBolks;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $isActive;


    public function __construct()
    {
        $this->userGroups = array();
        $this->name = "Brukerinndeling";
        $this->teams = array();
        $this->semesters = array();
        $this->assistantsDepartments = array();
        $this->assistantBolks = array();
        $this->numberUserGroups = 2;
        $this->isActive = false;
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
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     */
    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }



}

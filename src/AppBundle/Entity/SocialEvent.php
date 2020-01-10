<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="event")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\SocialEventRepository")
 */
class SocialEvent
{

    /**
     * @var Department
     *
     * @ORM\ManyToOne(targetEntity="Department")
     * @@ORM\JoinColumn(referencedColumnName="id")
     */
    private $department;


    /**
     * @var Semester
     *
     * @ORM\ManyToOne(targetEntity="Semester")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    private $semester;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=5000)
     */
    private $description;


    /**
     * @ORM\Column(type="datetime")
     */
    private $startTime;


    /**
     * @ORM\Column(type="datetime")
     */
    private $endTime;

    /**
     * @var Role
     * @ORM\ManyToOne(targetEntity="Role")
     * @ORM\JoinColumn(referencedColumnName="id")
     *
     */
    private $role;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     * @Assert\Length(max=250)
     */
    private $link;


    /**
     * Constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLink(): ?string
    {
        return $this->link;
    }

    /**
     * @param string $link
     * @return SocialEvent
     */
    public function setLink($link): SocialEvent
    {
        $this->link = $link;
        return $this;
    }


    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return SocialEvent
     */
    public function setTitle($title): SocialEvent
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return SocialEvent
     */
    public function setDescription($description): SocialEvent
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStartTime(): ?DateTime
    {
        return $this->startTime;
    }

    /**
     * @param DateTime $startTime
     * @return SocialEvent
     */
    public function setStartTime($startTime): SocialEvent
    {
        $this->startTime = $startTime;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEndTime(): ?DateTime
    {
        return $this->endTime;
    }

    /**
     * @param DateTime $endTime
     * @return SocialEvent
     */
    public function setEndTime($endTime): SocialEvent
    {
        $this->endTime = $endTime;
        return $this;
    }


    /**
     * @return Department
     */
    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    /**
     * @param Department|null $department
     * @return SocialEvent
     */
    public function setDepartment(? Department $department): SocialEvent
    {
        $this->department = $department;
        return $this;
    }

    /**
     * @param Semester|null $semester
     * @return $this
     */
    public function setSemester(? Semester $semester)
    {
        $this->semester = $semester;
        return $this;
    }

    /**
     * @return Semester
     */
    public function getSemester(): ?Semester
    {
        return $this->semester;
    }

    /**
     * @return Role|null
     */
    public function getRole(): ?Role
    {
        return $this->role;
    }

    /**
     * @param Role $role
     * @return SocialEvent
     */
    public function setRole(Role $role): SocialEvent
    {
        $this->role = $role;
        return $this;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function hasHappened(): bool
    {
        return $this->getStartTime() < new DateTime();
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function happensSoon(): bool
    {
        return (!($this->hasHappened()) && $this->getStartTime() < new DateTime('+1 week'));
    }
}

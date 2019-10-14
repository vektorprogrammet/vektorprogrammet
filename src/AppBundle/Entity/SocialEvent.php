<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="event")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\SocialEventItemRepository")
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
     * @ORM\Column(type="string")
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
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
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param \DateTime $startTime
     */
    public function setStartTime($startTime): void
    {
        $this->startTime = $startTime;
    }

    /**
     * @return \DateTime
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param \DateTime $endTime
     */
    public function setEndTime($endTime): void
    {
        $this->endTime = $endTime;
    }


    /**
     * @return Department
     */
    public function getDepartment(): ? Department
    {
        return $this->department;
    }

    /**
     * @param Department $department
     *
     */
    public function setDepartment(? Department $department)
    {
        $this->department = $department;
    }

    /**
     * @param \AppBundle\Entity\Semester|null $semester
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
    public function getSemester(): ? Semester
    {
        return $this->semester;
    }

    /**
     * @return Role|null
     */
    public function getRole(): ? Role
    {
        return $this->role;
    }

    /**
     * @param Role $role
     */
    public function setRole(Role $role): void
    {
        $this->role = $role;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function hasHappened(): bool
    {
        return $this->getStartTime() < new \DateTime();
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function happensSoon(): bool
    {
        return (!($this->hasHappened()) && $this->getStartTime() < new \DateTime('+1 week'));
    }
}

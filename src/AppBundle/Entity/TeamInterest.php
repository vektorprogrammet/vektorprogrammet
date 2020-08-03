<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TeamInterest
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="TeamInterestRepository")
 */
class TeamInterest implements DepartmentSemesterInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     * @Assert\Length(max="255", maxMessage="Navnet ditt kan maksimalt være 255 tegn")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     * @Assert\Length(max="255", maxMessage="Emailen din kan maksimalt være 255 tegn")
     * @Assert\Email(message="Emailen din er ikke formatert riktig")
     */
    private $email;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime")
     */
    private $timestamp;

    /**
     * @var Team[]
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Team", inversedBy="potentialApplicants")
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     * @Assert\Count(min=1, minMessage="Du må velge minst ett team")
     */
    private $potentialTeams;

    /**
     * @var Semester
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Semester")
     */
    private $semester;

    /**
     * @var Department
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Department")
     */
    private $department;

    /**
     * TeamInterest constructor.
     */
    public function __construct()
    {
        $this->timestamp = new DateTime();
    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get name
     *
     * @param string $name
     *
     * @return TeamInterest
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return TeamInterest
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Get timestamp
     *
     * @return DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Get potentialTeams
     *
     * @return Team[]
     */
    public function getPotentialTeams()
    {
        return $this->potentialTeams;
    }

    /**
     * Set potentialTeams
     *
     * @param Team[] $potentialTeams
     * @return TeamInterest
     */
    public function setPotentialTeams($potentialTeams)
    {
        $this->potentialTeams = $potentialTeams;

        return $this;
    }

    /**
     * Get semester
     *
     * @return Semester
     */
    public function getSemester()
    {
        return $this->semester;
    }

    /**
     * Set semester
     *
     * @param Semester $semester
     * @return TeamInterest
     */
    public function setSemester(Semester $semester)
    {
        $this->semester = $semester;

        return $this;
    }

    /**
     * @return Department
     */
    public function getDepartment(): Department
    {
        return $this->department;
    }

    /**
     * @param Department $department
     *
     * @return TeamInterest
     */
    public function setDepartment(Department $department): TeamInterest
    {
        $this->department = $department;
        return $this;
    }
}

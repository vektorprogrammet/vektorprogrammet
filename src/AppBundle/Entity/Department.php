<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="department")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\DepartmentRepository")
 * @JMS\ExclusionPolicy("all")
 */
class Department
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose()
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=250)
     * @Assert\NotBlank
     * @JMS\Expose()
     */
    private $name;

    /**
     * @ORM\Column(name="short_name", type="string", length=50)
     * @Assert\NotBlank
     * @JMS\Expose()
     */
    private $shortName;

    /**
     * @ORM\Column(type="string", length=250)
     * @Assert\NotBlank
     * @Assert\Email
     * @JMS\Expose()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=250)
     * @Assert\NotBlank
     * @JMS\Expose()
     */
    protected $address;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     * @Assert\NotBlank
     * @JMS\Expose()
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $slackChannel;

    /**
     * @ORM\ManyToMany(targetEntity="School", inversedBy="departments")
     * @ORM\JoinTable(name="department_school")
     * @ORM\JoinColumn(onDelete="cascade")
     * @JMS\Expose()
     **/
    protected $schools;

    /**
     * @ORM\OneToMany(targetEntity="FieldOfStudy", mappedBy="department", cascade={"remove"})
     * @JMS\Expose()
     */
    private $fieldOfStudy;

    /**
     * @ORM\OneToMany(targetEntity="Semester", mappedBy="department",  cascade={"remove"})
     * @ORM\OrderBy({"semesterStartDate" = "DESC"})
     **/
    private $semesters;

    /**
     * @ORM\OneToMany(targetEntity="Team", mappedBy="department", cascade={"remove"})
     * @JMS\Expose()
     **/
    private $teams;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->schools = new ArrayCollection();
        $this->fieldOfStudy = new ArrayCollection();
        $this->semesters = new ArrayCollection();
        $this->teams = new ArrayCollection();
    }

    /**
     * @return Semester
     */
    public function getCurrentSemester()
    {
        $now = new \DateTime();

        foreach ($this->semesters as $semester) {
            if ($now > $semester->getSemesterStartDate() && $now < $semester->getSemesterEndDate()) {
                // Current semester
                return $semester;
            }
        }

        return null;
    }

    /**
     * @return Semester
     */
    public function getLatestSemester()
    {
        /** @var Semester[] $semesters */
        $semesters = $this->getSemesters()->toArray();

        $latestSemester = current($semesters);

        $now = new \DateTime();

        foreach ($semesters as $semester) {
            if ($semester->getSemesterStartDate() < $now && $semester->getSemesterEndDate() > $latestSemester->getSemesterEndDate()) {
                $latestSemester = $semester;
            }
        }

        return $latestSemester;
    }

    /**
     * @return Semester
     */
    public function getCurrentOrLatestSemester()
    {
        if (null === $semester = $this->getCurrentSemester()) {
            $semester = $this->getLatestSemester();
        }

        return $semester;
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
     * @return Department
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
     * Set shortName.
     *
     * @param string $shortName
     *
     * @return Department
     */
    public function setShortName($shortName)
    {
        $this->shortName = $shortName;

        return $this;
    }

    /**
     * Get shortName.
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * Add fieldOfStudy.
     *
     * @param FieldOfStudy $fieldOfStudy
     *
     * @return Department
     */
    public function addFieldOfStudy(FieldOfStudy $fieldOfStudy)
    {
        $this->fieldOfStudy[] = $fieldOfStudy;

        return $this;
    }

    /**
     * Remove fieldOfStudy.
     *
     * @param FieldOfStudy $fieldOfStudy
     */
    public function removeFieldOfStudy(FieldOfStudy $fieldOfStudy)
    {
        $this->fieldOfStudy->removeElement($fieldOfStudy);
    }

    /**
     * Get fieldOfStudy.
     *
     * @return ArrayCollection
     */
    public function getFieldOfStudy()
    {
        return $this->fieldOfStudy;
    }

    public function __toString()
    {
        return (string) $this->getShortName();
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return Department
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Add schools.
     *
     * @param School $schools
     *
     * @return Department
     */
    public function addSchool(School $schools)
    {
        $this->schools[] = $schools;

        return $this;
    }

    /**
     * Remove schools.
     *
     * @param School $schools
     */
    public function removeSchool(School $schools)
    {
        $this->schools->removeElement($schools);
    }

    /**
     * Get schools.
     *
     * @return ArrayCollection
     */
    public function getSchools()
    {
        return $this->schools;
    }

    /**
     * Set address.
     *
     * @param string $address
     *
     * @return Department
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address.
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Add semesters.
     *
     * @param Semester $semesters
     *
     * @return Department
     */
    public function addSemester(Semester $semesters)
    {
        $this->semesters[] = $semesters;

        return $this;
    }

    /**
     * Remove semesters.
     *
     * @param Semester $semesters
     */
    public function removeSemester(Semester $semesters)
    {
        $this->semesters->removeElement($semesters);
    }

    /**
     * Get semesters.
     *
     * @return ArrayCollection
     */
    public function getSemesters()
    {
        return $this->semesters;
    }

    /**
     * Add teams.
     *
     * @param Team $teams
     *
     * @return Department
     */
    public function addTeam(Team $teams)
    {
        $this->teams[] = $teams;

        return $this;
    }

    /**
     * Remove teams.
     *
     * @param Team $teams
     */
    public function removeTeam(Team $teams)
    {
        $this->teams->removeElement($teams);
    }

    /**
     * Get teams.
     *
     * @return ArrayCollection
     */
    public function getTeams()
    {
        return $this->teams;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    // Used for unit testing
    public function fromArray($data = array())
    {
        foreach ($data as $property => $value) {
            $method = "set{$property}";
            $this->$method($value);
        }
    }

    /**
     * @return string
     */
    public function getSlackChannel()
    {
        return $this->slackChannel;
    }

    /**
     * @param string $slackChannel
     */
    public function setSlackChannel($slackChannel)
    {
        $this->slackChannel = $slackChannel;
    }
}

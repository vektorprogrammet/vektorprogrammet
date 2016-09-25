<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="department")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\DepartmentRepository")
 */
class Department
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=250)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank
     */
    private $short_name;

    /**
     * @ORM\Column(type="string", length=250)
     * @Assert\NotBlank
     * @Assert\Email
     */
    private $email;

    /**
     * @ORM\ManyToMany(targetEntity="School", inversedBy="departments")
     * @ORM\JoinTable(name="department_school")
     * @ORM\JoinColumn(onDelete="cascade")
     **/
    protected $schools;

    /**
     * @ORM\OneToMany(targetEntity="FieldOfStudy", mappedBy="department", cascade={"remove"})
     */
    private $fieldOfStudy;

    /**
     * @ORM\Column(type="string", length=250)
     * @Assert\NotBlank
     */
    protected $address;

    /**
     * @ORM\OneToMany(targetEntity="Semester", mappedBy="department",  cascade={"remove"})
     **/
    private $semesters;

    /**
     * @ORM\OneToMany(targetEntity="Team", mappedBy="department", cascade={"remove"})
     **/
    private $teams;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->schools = new \Doctrine\Common\Collections\ArrayCollection();
        $this->fieldOfStudy = new \Doctrine\Common\Collections\ArrayCollection();
        $this->semesters = new \Doctrine\Common\Collections\ArrayCollection();
        $this->teams = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set short_name.
     *
     * @param string $shortName
     *
     * @return Department
     */
    public function setShortName($shortName)
    {
        $this->short_name = $shortName;

        return $this;
    }

    /**
     * Set short_name.
     *
     * @param string $shortName
     *
     * @return Department
     */
    public function setShort_Name($shortName)
    {
        $this->short_name = $shortName;

        return $this;
    }

    /**
     * Get short_name.
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->short_name;
    }

    /**
     * Add fieldOfStudy.
     *
     * @param \AppBundle\Entity\FieldOfStudy $fieldOfStudy
     *
     * @return Department
     */
    public function addFieldOfStudy(\AppBundle\Entity\FieldOfStudy $fieldOfStudy)
    {
        $this->fieldOfStudy[] = $fieldOfStudy;

        return $this;
    }

    /**
     * Remove fieldOfStudy.
     *
     * @param \AppBundle\Entity\FieldOfStudy $fieldOfStudy
     */
    public function removeFieldOfStudy(\AppBundle\Entity\FieldOfStudy $fieldOfStudy)
    {
        $this->fieldOfStudy->removeElement($fieldOfStudy);
    }

    /**
     * Get fieldOfStudy.
     *
     * @return \Doctrine\Common\Collections\Collection
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
     * @param \AppBundle\Entity\School $schools
     *
     * @return Department
     */
    public function addSchool(\AppBundle\Entity\School $schools)
    {
        $this->schools[] = $schools;

        return $this;
    }

    /**
     * Remove schools.
     *
     * @param \AppBundle\Entity\School $schools
     */
    public function removeSchool(\AppBundle\Entity\School $schools)
    {
        $this->schools->removeElement($schools);
    }

    /**
     * Get schools.
     *
     * @return \Doctrine\Common\Collections\Collection
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
     * @param \AppBundle\Entity\Semester $semesters
     *
     * @return Department
     */
    public function addSemester(\AppBundle\Entity\Semester $semesters)
    {
        $this->semesters[] = $semesters;

        return $this;
    }

    /**
     * Remove semesters.
     *
     * @param \AppBundle\Entity\Semester $semesters
     */
    public function removeSemester(\AppBundle\Entity\Semester $semesters)
    {
        $this->semesters->removeElement($semesters);
    }

    /**
     * Get semesters.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSemesters()
    {
        return $this->semesters;
    }

    /**
     * Add teams.
     *
     * @param \AppBundle\Entity\Team $teams
     *
     * @return Department
     */
    public function addTeam(\AppBundle\Entity\Team $teams)
    {
        $this->teams[] = $teams;

        return $this;
    }

    /**
     * Remove teams.
     *
     * @param \AppBundle\Entity\Team $teams
     */
    public function removeTeam(\AppBundle\Entity\Team $teams)
    {
        $this->teams->removeElement($teams);
    }

    /**
     * Get teams.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTeams()
    {
        return $this->teams;
    }

    // Used for unit testing
    public function fromArray($data = array())
    {
        foreach ($data as $property => $value) {
            $method = "set{$property}";
            $this->$method($value);
        }
    }
}

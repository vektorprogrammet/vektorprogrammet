<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\SchoolCapacityRepository")
 * @ORM\Table(name="school_capacity")
 * @ORM\HasLifecycleCallbacks
 */
class SchoolCapacity
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="School")
     * @ORM\JoinColumn(name="school_id", referencedColumnName="id")
     */
    protected $school;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Semester")
     * @ORM\JoinColumn(name="semester_id", referencedColumnName="id")
     */
    protected $semester;

    /**
     * @ORM\Column(name="monday",type="integer")
     */
    protected $monday;

    /**
     * @ORM\Column(name="tuesday",type="integer")
     */
    protected $tuesday;

    /**
     * @ORM\Column(name="wednesday",type="integer")
     */
    protected $wednesday;

    /**
     * @ORM\Column(name="thursday",type="integer")
     */
    protected $thursday;

    /**
     * @ORM\Column(name="friday",type="integer")
     */
    protected $friday;

    /**
     * SchoolCapacity constructor.
     */
    public function __construct()
    {
        $this->monday = 0;
        $this->tuesday = 0;
        $this->wednesday = 0;
        $this->thursday = 0;
        $this->friday = 0;
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
     * @return int
     */
    public function getMonday()
    {
        return $this->monday;
    }

    /**
     * @param int $monday
     */
    public function setMonday($monday)
    {
        $this->monday = $monday;
    }

    /**
     * @return int
     */
    public function getTuesday()
    {
        return $this->tuesday;
    }

    /**
     * @param int $tuesday
     */
    public function setTuesday($tuesday)
    {
        $this->tuesday = $tuesday;
    }

    /**
     * @return int
     */
    public function getWednesday()
    {
        return $this->wednesday;
    }

    /**
     * @param int $wednesday
     */
    public function setWednesday($wednesday)
    {
        $this->wednesday = $wednesday;
    }

    /**
     * @return int
     */
    public function getThursday()
    {
        return $this->thursday;
    }

    /**
     * @param int $thursday
     */
    public function setThursday($thursday)
    {
        $this->thursday = $thursday;
    }

    /**
     * @return int
     */
    public function getFriday()
    {
        return $this->friday;
    }

    /**
     * @param int $friday
     */
    public function setFriday($friday)
    {
        $this->friday = $friday;
    }

    /**
     * @return School
     */
    public function getSchool()
    {
        return $this->school;
    }

    /**
     * @param School $school
     */
    public function setSchool($school)
    {
        $this->school = $school;
    }

    /**
     * @return Semester
     */
    public function getSemester()
    {
        return $this->semester;
    }

    /**
     * @param Semester $semester
     */
    public function setSemester($semester)
    {
        $this->semester = $semester;
    }
}

<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="semester")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\SemesterRepository")
 */
class Semester
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     */
    protected $semesterTime;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     */
    protected $year;

    /**
     * @var Department
     * @ORM\ManyToOne(targetEntity="Department", inversedBy="semesters")
     */
    protected $department;

    /**
     * @ORM\Column(name="admission_start_date", type="datetime", length=150)
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     */
    protected $admissionStartDate;

    /**
     * @ORM\Column(name="admission_end_date", type="datetime", length=150)
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     */
    protected $admissionEndDate;

    /**
     * @ORM\Column(type="datetime", length=150)
     */
    protected $semesterStartDate;

    /**
     * @ORM\Column(type="datetime", length=150)
     */
    protected $semesterEndDate;

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
     * Set admissionStartDate.
     *
     * @param \DateTime $admissionStartDate
     *
     * @return Semester
     */
    public function setAdmissionStartDate($admissionStartDate)
    {
        $this->admissionStartDate = $admissionStartDate;

        return $this;
    }

    /**
     * Get admissionStartDate.
     *
     * @return \DateTime
     */
    public function getAdmissionStartDate()
    {
        return $this->admissionStartDate;
    }

    /**
     * Set admissionEndDate.
     *
     * @param \DateTime $admissionEndDate
     *
     * @return Semester
     */
    public function setAdmissionEndDate($admissionEndDate)
    {
        $this->admissionEndDate = $admissionEndDate;

        return $this;
    }

    /**
     * Get admissionEndDate.
     *
     * @return \DateTime
     */
    public function getAdmissionEndDate()
    {
        return $this->admissionEndDate;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->semesterTime.' '.$this->year;
    }

    /**
     * Set department.
     *
     * @param \AppBundle\Entity\Department $department
     *
     * @return Semester
     */
    public function setDepartment(\AppBundle\Entity\Department $department = null)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department.
     *
     * @return \AppBundle\Entity\Department
     */
    public function getDepartment()
    {
        return $this->department;
    }

    public function __toString()
    {
        return (string) $this->getName().' - '.$this->getDepartment(); //Fix for viewing departmentname in semesterlist.
    }

    /**
     * Set semesterStartDate.
     *
     * @param \DateTime $semesterStartDate
     *
     * @return Semester
     */
    public function setSemesterStartDate($semesterStartDate)
    {
        $this->semesterStartDate = $semesterStartDate;

        return $this;
    }

    /**
     * Get semesterStartDate.
     *
     * @return \DateTime
     */
    public function getSemesterStartDate()
    {
        return $this->semesterStartDate;
    }

    /**
     * Set semesterEndDate.
     *
     * @param \DateTime $semesterEndDate
     *
     * @return Semester
     */
    public function setSemesterEndDate($semesterEndDate)
    {
        $this->semesterEndDate = $semesterEndDate;

        return $this;
    }

    /**
     * Get semesterEndDate.
     *
     * @return \DateTime
     */
    public function getSemesterEndDate()
    {
        return $this->semesterEndDate;
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
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param string $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    /**
     * @return string
     */
    public function getSemesterTime()
    {
        return $this->semesterTime;
    }

    /**
     * @param string $semesterTime
     */
    public function setSemesterTime($semesterTime)
    {
        $this->semesterTime = $semesterTime;
    }

    public function setStartAndEndDateByTime(string $time, string $year)
    {
        $startMonth = $time == 'Vår' ? '01' : '08';
        $endMonth = $time == 'Vår' ? '07' : '12';

        $this->setSemesterStartDate(date_create($year.'-'.$startMonth.'-01 00:00:00'));
        $this->setSemesterEndDate(date_create($year.'-'.$endMonth.'-31 23:59:59'));
    }
}

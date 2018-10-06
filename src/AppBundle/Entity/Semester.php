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
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     */
    private $semesterTime;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     */
    private $year;

    /**
     * @ORM\Column(type="datetime", length=150)
     */
    private $semesterStartDate;

    /**
     * @ORM\Column(type="datetime", length=150)
     */
    private $semesterEndDate;

    /**
     * @var AdmissionPeriod[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\AdmissionPeriod", mappedBy="semester")
     */
    private $admissionPeriods;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
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
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->semesterTime.' '.$this->year;
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

    public function isActive(): bool
    {
        $now = new \DateTime();

        return $this->getSemesterStartDate() < $now && $now <= $this->getSemesterEndDate();
    }

    /**
     * @return AdmissionPeriod[]
     */
    public function getAdmissionPeriods()
    {
        return $this->admissionPeriods;
    }

    /**
     * @param AdmissionPeriod $admissionPeriods
     *
     * @return Semester
     */
    public function setAdmissionPeriods($admissionPeriods)
    {
        $this->admissionPeriods = $admissionPeriods;
        return $this;
    }
}

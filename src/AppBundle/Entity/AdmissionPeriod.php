<?php

namespace AppBundle\Entity;

use AppBundle\Utils\TimeUtil;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DepartmentSpecificSemester
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\AdmissionPeriodRepository")
 */
class AdmissionPeriod implements PeriodInterface
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
     * @var Department
     * @ORM\ManyToOne(targetEntity="Department", inversedBy="admissionPeriods")
     */
    private $department;

    /**
     * @ORM\Column(name="start_date", type="datetime", length=150)
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     */
    private $startDate;

    /**
     * @ORM\Column(name="end_date", type="datetime", length=150)
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     */
    private $endDate;

    /**
     * @var InfoMeeting
     * @ORM\OneToOne(targetEntity="InfoMeeting", cascade={"remove", "persist"})
     * @Assert\Valid
     */
    private $infoMeeting;

    /**
     * @var Semester
     * @ORM\ManyToOne(targetEntity="Semester", inversedBy="admissionPeriods")
     */
    private $semester;

    public function __toString()
    {
        return (string) $this->semester->getName().' - '.$this->getDepartment();
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
     * Set department.
     *
     * @param \AppBundle\Entity\Department $department
     *
     * @return AdmissionPeriod
     */
    public function setDepartment(Department $department = null)
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

    /**
     * Set startDate.
     *
     * @param DateTime $startDate
     *
     * @return AdmissionPeriod
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate.
     *
     * @return DateTime
     */
    public function getStartDate(): \DateTime
    {
        return $this->startDate;
    }

    /**
     * Set endDate.
     *
     * @param DateTime $admissionEndDate
     * @param DateTime $endDate
     *
     * @return AdmissionPeriod
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate.
     *
     * @return DateTime
     */
    public function getEndDate(): \DateTime
    {
        return $this->endDate;
    }

    /**
     * @return InfoMeeting
     */
    public function getInfoMeeting()
    {
        return $this->infoMeeting;
    }

    /**
     * @param InfoMeeting $infoMeeting
     */
    public function setInfoMeeting($infoMeeting)
    {
        $this->infoMeeting = $infoMeeting;
    }


    public function isActive(): bool
    {
        $now = new DateTime();

        return $this->semester->getStartDate() < $now && $now <= $this->semester->getEndDate();
    }

    public function hasActiveAdmission(): bool
    {
        $now = new DateTime();

        return $this->getStartDate() <= $now && $now <= $this->getEndDate();
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
     *
     * @return AdmissionPeriod
     */
    public function setSemester($semester)
    {
        $this->semester = $semester;
        return $this;
    }

    public function shouldSendInfoMeetingNotifications()
    {
        return $this->infoMeeting !== null &&
            $this->infoMeeting->getDate() !== null &&
            $this->infoMeeting->isShowOnPage() &&
            TimeUtil::dateTimeIsToday($this->infoMeeting->getDate()) &&
            TimeUtil::dateTimeIsInTheFuture($this->infoMeeting->getDate());
    }
}

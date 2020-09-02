<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\AdmissionNotificationRepository")
 * @ORM\Table(name="admission_notification")
 *
 */
class AdmissionNotification
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /**
     * @ORM\ManyToOne(targetEntity="AdmissionSubscriber")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $subscriber;

    /**
     * @ORM\ManyToOne(targetEntity="Semester")
     */
    private $semester;

    /**
     * @ORM\Column(type="boolean")
     */
    private $infoMeeting;

    /**
     * @var Department
     * @ORM\ManyToOne(targetEntity="Department")
     */
    private $department;

    public function __construct()
    {
        $this->timestamp = new DateTime();
        $this->infoMeeting = false;
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
     * @return DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param DateTime $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return AdmissionSubscriber
     */
    public function getSubscriber()
    {
        return $this->subscriber;
    }

    /**
     * @param AdmissionSubscriber $subscriber
     */
    public function setSubscriber($subscriber)
    {
        $this->subscriber = $subscriber;
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

    /**
     * @return boolean
     */
    public function getInfoMeeting()
    {
        return $this->infoMeeting;
    }

    /**
     * @param boolean $bool
     */
    public function setInfoMeeting($bool)
    {
        $this->infoMeeting = $bool;
    }

    /**
     * @return Department
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * @param Department $department
     *
     * @return AdmissionNotification
     */
    public function setDepartment(Department $department)
    {
        $this->department = $department;
        return $this;
    }
}

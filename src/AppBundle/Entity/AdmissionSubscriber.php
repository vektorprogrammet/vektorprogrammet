<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\AdmissionSubscriberRepository")
 * @ORM\Table(name="admission_subscriber")
 *
 * @UniqueEntity(
 *      fields={"unsubscribeCode"}
 * )
 */
class AdmissionSubscriber
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="E-post må fylles inn")
     * @Assert\Email(message="Dette er ikke en gyldig e-postadresse")
     */
    private $email;

    /**
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /**
     * @var Department
     * @ORM\ManyToOne(targetEntity="Department")
     */
    private $department;

    /**
     * @ORM\Column(type="string")
     */
    private $unsubscribeCode;

    /**
     * @ORM\Column(type="boolean", options={"default"=false})
     */
    private $fromApplication;

    /**
     * @ORM\Column(type="boolean", options={"default"=false})
     */
    private $infoMeeting;

    public function __construct()
    {
        $this->fromApplication = false;
        $this->infoMeeting = false;
        $this->timestamp = new DateTime();
        $this->unsubscribeCode = bin2hex(openssl_random_pseudo_bytes(12));
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
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
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
     * @return string
     */
    public function getUnsubscribeCode()
    {
        return $this->unsubscribeCode;
    }

    /**
     * @param string $unsubscribeCode
     */
    public function setUnsubscribeCode($unsubscribeCode)
    {
        $this->unsubscribeCode = $unsubscribeCode;
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
     */
    public function setDepartment($department): void
    {
        $this->department = $department;
    }

    public function __toString()
    {
        return $this->department->getCity();
    }

    /**
     * @return bool
     */
    public function getFromApplication()
    {
        return $this->fromApplication;
    }

    /**
     * @param bool $fromApplication
     */
    public function setFromApplication($fromApplication): void
    {
        $this->fromApplication = $fromApplication;
    }

    /**
     * @return bool
     */
    public function getInfoMeeting()
    {
        return $this->infoMeeting;
    }

    /**
     * @param bool $infoMeeting
     */
    public function setInfoMeeting($infoMeeting): void
    {
        $this->infoMeeting = $infoMeeting;
    }
}

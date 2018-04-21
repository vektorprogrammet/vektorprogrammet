<?php

namespace AppBundle\Entity;

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
     * @Assert\Email(message="Dette er ikke en gylid e-postadresse")
     */
    private $email;

    /**
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /**
     * @ORM\ManyToOne(targetEntity="Department")
     */
    private $department;

    /**
     * @ORM\Column(type="string")
     */
    private $unsubscribeCode;

    /**
     * Constructor.
     *
     * @param Department $department
     */
    public function __construct(Department $department = null)
    {
        $this->timestamp = new \DateTime();
        $this->unsubscribeCode = bin2hex(openssl_random_pseudo_bytes(12));
        $this->setDepartment($department);
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
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param \DateTime $timestamp
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
}

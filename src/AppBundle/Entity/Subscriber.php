<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\SubscriberRepository")
 * @ORM\Table(name="subscriber")
 *
 * @UniqueEntity(
 *      fields={"unsubscribeCode"}
 * )
 */
class Subscriber
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $email;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /**
     * @ORM\ManyToOne(targetEntity="Newsletter", inversedBy="subscribers")
     */
    private $newsletter;

    /**
     * @ORM\Column(type="string")
     */
    private $unsubscribeCode;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->timestamp = new \DateTime();
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
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param mixed $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return mixed
     */
    public function getNewsletter()
    {
        return $this->newsletter;
    }

    /**
     * @param mixed $newsletter
     */
    public function setNewsletter($newsletter)
    {
        $this->newsletter = $newsletter;
    }
    /**
     * @param mixed
     */
    public function getUnsubscribeCode()
    {
        return $this->unsubscribeCode;
    }
    /**
     * @param mixed $unsubscribeCode
     */
    public function setUnsubscribeCode($unsubscribeCode)
    {
        $this->unsubscribeCode = $unsubscribeCode;
    }
}

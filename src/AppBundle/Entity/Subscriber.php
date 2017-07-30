<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotBlank
     */
    private $email;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank
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
     *
     * @param Newsletter $newsletter
     */
    public function __construct(Newsletter $newsletter = null)
    {
        $this->timestamp = new \DateTime();
        $this->unsubscribeCode = bin2hex(openssl_random_pseudo_bytes(12));
        $this->setNewsletter($newsletter);
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * @return Newsletter
     */
    public function getNewsletter()
    {
        return $this->newsletter;
    }

    /**
     * @param Newsletter $newsletter
     */
    public function setNewsletter($newsletter)
    {
        $this->newsletter = $newsletter;
    }

    /**
     * @param string
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
}

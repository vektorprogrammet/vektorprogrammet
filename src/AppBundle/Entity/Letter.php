<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\LetterRepository")
 * @ORM\Table(name="letter")
 */
class Letter
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
    private $title;

    /**
     * @ORM\Column(type="integer")
     */
    private $recipientCount;

    /**
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="Newsletter", inversedBy="letters")
     */
    private $newsletter;

    /**
     * Constructor.
     *
     * @param Newsletter $newsletter
     */
    public function __construct(Newsletter $newsletter = null)
    {
        $this->setNewsletter($newsletter);
        $this->setTimestamp(new \DateTime());
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
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
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
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
     * @return int
     */
    public function getRecipientCount()
    {
        return $this->recipientCount;
    }

    /**
     * @param int $recipientCount
     */
    public function setRecipientCount($recipientCount)
    {
        $this->recipientCount = $recipientCount;
    }

}

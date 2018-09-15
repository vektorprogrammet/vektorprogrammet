<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * WorkDay
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\WorkDayRepository")
 */
class WorkDay

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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var School
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\School")
     */
    private $school;

    /**
     * @var integer
     *
     * @ORM\Column(name="term", type="integer")
     *
     * @Assert\Range(min="1", max="2")
     */
    private $term;

    /**
     * @var AssistantHistory
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\AssistantHistory", inversedBy="workDays")
     */
    private $assistantPosition;

    /**
     * @var SubstitutePosition
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SubstitutePosition", inversedBy="workDays")
     */
    private $substitutePosition;

    /**
     * WorkDay constructor.
     *
     * @param AssistantHistory $assistantPosition
     */
    public function __construct(AssistantHistory $assistantPosition)
    {
        $this->assistantPosition = $assistantPosition;
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return WorkDay
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return School
     */
    public function getSchool(): ?School
    {
        return $this->school;
    }

    /**
     * @param School $school
     *
     * @return WorkDay
     */
    public function setSchool(School $school): WorkDay
    {
        $this->school = $school;
        return $this;
    }

    /**
     * @return int
     */
    public function getTerm(): int
    {
        return $this->term;
    }

    /**
     * @return string
     */
    public function getTermAsString(): string
    {
        return "Bolk {$this->term}";
    }

    /**
     * @param int $term
     *
     * @return WorkDay
     */
    public function setTerm(int $term): WorkDay
    {
        $this->term = $term;
        return $this;
    }

    /**
     * @return AssistantHistory
     */
    public function getAssistantPosition(): ?AssistantHistory
    {
        return $this->assistantPosition;
    }

    /**
     * @param AssistantHistory $assistantPosition
     *
     * @return WorkDay
     */
    public function setAssistantPosition(AssistantHistory $assistantPosition): WorkDay
    {
        $this->assistantPosition = $assistantPosition;
        return $this;
    }

    /**
     * @return SubstitutePosition
     */
    public function getSubstitutePosition(): ?SubstitutePosition
    {
        return $this->substitutePosition;
    }

    /**
     * @param SubstitutePosition $substitutePosition
     *
     * @return WorkDay
     */
    public function setSubstitutePosition(SubstitutePosition $substitutePosition): WorkDay
    {
        $this->substitutePosition = $substitutePosition;
        return $this;
    }
}

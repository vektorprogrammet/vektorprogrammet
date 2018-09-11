<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WorkDay
 *
 * @ORM\Table()
 * @ORM\Entity
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
     * @var Term
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Term")
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
     * @return Term
     */
    public function getTerm(): Term
    {
        return $this->term;
    }

    /**
     * @param Term $term
     *
     * @return WorkDay
     */
    public function setTerm(Term $term): WorkDay
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

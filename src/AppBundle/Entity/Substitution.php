<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Substitution
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Substitution
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
     * @ORM\ManyToOne(targetEntity="School")
     */
    private $school;


    /**
     * @var SubstitutePosition
     *
     * @ORM\ManyToOne(targetEntity="SubstitutePosition", inversedBy="substitutions")
     */
    private $substitutePosition;

    /**
     * @var AssistantHistory
     *
     * @ORM\ManyToOne(targetEntity="AssistantHistory", inversedBy="substitutions")
     */
    private $substitutedAssistant;


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
     * @return Substitution
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
     * Set school
     *
     * @param School $school
     * @return Substitution
     */
    public function setSchool($school)
    {
        $this->school = $school;

        return $this;
    }

    /**
     * Get school
     *
     * @return School
     */
    public function getSchool()
    {
        return $this->school;
    }

    /**
     * Get substitute position
     *
     * @return SubstitutePosition
     */
    public function getSubstitutePosition(): SubstitutePosition
    {
        return $this->substitutePosition;
    }

    /**
     * Set substitute position
     *
     * @param SubstitutePosition $substitutePosition
     *
     * @return Substitution
     */
    public function setSubstitutePosition(SubstitutePosition $substitutePosition): Substitution
    {
        $this->substitutePosition = $substitutePosition;
        return $this;
    }

    /**
     * Get substituted assistant
     *
     * @return AssistantHistory
     */
    public function getSubstitutedAssistant()
    {
        return $this->substitutedAssistant;
    }

    /**
     * Set substituted assistant
     *
     * @param AssistantHistory $substitutedAssistant
     * @return Substitution
     */
    public function setSubstitutedAssistant($substitutedAssistant)
    {
        $this->substitutedAssistant = $substitutedAssistant;

        return $this;
    }
}

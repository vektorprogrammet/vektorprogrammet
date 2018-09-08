<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WorkDay
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class PartnerWorkDay
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
     * @var AssistantHistory[]
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\AssistantHistory", mappedBy="partnerWorkDays")
     */
    private $assistantPosisions;

    /**
     * @var SubstitutePosition[]
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\SubstitutePosition", mappedBy="partnerWorkDays")
     */
    private $substitutePositions;


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
     * @return PartnerWorkDay
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
     * @return PartnerWorkDay
     */
    public function setTerm(Term $term): PartnerWorkDay
    {
        $this->term = $term;
        return $this;
    }

    /**
     * @return AssistantHistory[]
     */
    public function getAssistantPosisions(): array
    {
        return $this->assistantPosisions;
    }

    /**
     * @param AssistantHistory[] $assistantPosisions
     *
     * @return PartnerWorkDay
     */
    public function setAssistantPosisions(array $assistantPosisions): PartnerWorkDay
    {
        $this->assistantPosisions = $assistantPosisions;
        return $this;
    }

    /**
     * @return SubstitutePosition[]
     */
    public function getSubstitutePositions(): array
    {
        return $this->substitutePositions;
    }

    /**
     * @param SubstitutePosition[] $substitutePositions
     *
     * @return PartnerWorkDay
     */
    public function setSubstitutePositions(array $substitutePositions): PartnerWorkDay
    {
        $this->substitutePositions = $substitutePositions;
        return $this;
    }
}

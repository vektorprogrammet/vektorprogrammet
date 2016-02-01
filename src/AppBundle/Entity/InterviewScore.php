<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="interview_score")
 */
class InterviewScore
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $explanatoryPower;

    /**
     * @ORM\Column(type="integer")
     */
    protected $roleModel;

    /**
     * @ORM\Column(type="integer")
     */
    protected $suitability;

    /**
     * @ORM\OneToOne(targetEntity="ApplicationStatistic", inversedBy="interviewScore")
     * @ORM\JoinColumn(name="application_statistic_id", referencedColumnName="id")
     */
    protected $applicationStatistic;

    /**
     * @ORM\Column(type="string")
     */
    protected $suitableAssistant;

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
     * Set applicationStatistic
     *
     * @param \AppBundle\Entity\ApplicationStatistic $applicationStatistic
     * @return InterviewScore
     */
    public function setApplicationStatistic(\AppBundle\Entity\ApplicationStatistic $applicationStatistic = null)
    {
        $this->applicationStatistic = $applicationStatistic;

        return $this;
    }

    /**
     * Get applicationStatistic
     *
     * @return \AppBundle\Entity\ApplicationStatistic
     */
    public function getApplicationStatistic()
    {
        return $this->applicationStatistic;
    }

    /**
     * Set explanatoryPower
     *
     * @param integer $explanatoryPower
     * @return InterviewScore
     */
    public function setExplanatoryPower($explanatoryPower)
    {
        $this->explanatoryPower = $explanatoryPower;

        return $this;
    }

    /**
     * Get explanatoryPower
     *
     * @return integer 
     */
    public function getExplanatoryPower()
    {
        return $this->explanatoryPower;
    }

    /**
     * Set roleModel
     *
     * @param integer $roleModel
     * @return InterviewScore
     */
    public function setRoleModel($roleModel)
    {
        $this->roleModel = $roleModel;

        return $this;
    }

    /**
     * Get roleModel
     *
     * @return integer 
     */
    public function getRoleModel()
    {
        return $this->roleModel;
    }

    /**
     * Set suitability
     *
     * @param integer $suitability
     * @return InterviewScore
     */
    public function setSuitability($suitability)
    {
        $this->suitability = $suitability;

        return $this;
    }

    /**
     * Get suitability
     *
     * @return integer 
     */
    public function getSuitability()
    {
        return $this->suitability;
    }

    /**
     * Get the sum of all the scores
     *
     * @return integer
     */
    public function getSum()
    {
        return $this->explanatoryPower + $this->roleModel + $this->suitability;

    }

    /**
     * @return string
     */
    public function getSuitableAssistant()
    {
        return $this->suitableAssistant;
    }

    /**
     * @param string $suitableAssistant
     */
    public function setSuitableAssistant($suitableAssistant)
    {
        $this->suitableAssistant = $suitableAssistant;
    }
}



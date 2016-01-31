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
    protected $drive;

    /**
     * @ORM\Column(type="integer")
     */
    protected $totalImpression;




    /**
     * @ORM\OneToOne(targetEntity="ApplicationStatistic", inversedBy="interviewScore")
     * @ORM\JoinColumn(name="application_statistic_id", referencedColumnName="id")
     */
    protected $applicationStatistic;

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
     * Set drive
     *
     * @param integer $drive
     * @return InterviewScore
     */
    public function setDrive($drive)
    {
        $this->drive = $drive;

        return $this;
    }

    /**
     * Get drive
     *
     * @return integer 
     */
    public function getDrive()
    {
        return $this->drive;
    }

    /**
     * Get the sum of all the scores
     *
     * @return integer
     */
    public function getSum()
    {
        return $this->explanatoryPower + $this->roleModel + $this->drive + $this->totalImpression;

    }

    /**
     * Set totalImpression
     *
     * @param integer $totalImpression
     * @return InterviewScore
     */

    public function setTotalImpression($totalImpression)
    {
        $this->totalImpression = $totalImpression;

        return $this;
    }

    /**
     * Get totalImpression
     *
     * @return integer 
     */
    public function getTotalImpression()
    {
        return $this->totalImpression;
    }


}



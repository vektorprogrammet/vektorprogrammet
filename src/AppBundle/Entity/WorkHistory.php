<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @ORM\Table(name="WorkHistory")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\WorkHistoryRepository")
 */
class WorkHistory {
	
	 /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
	protected $id;
	
	/**
     * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(onDelete="SET NULL")
     **/
    protected $user;
	
	/**
     * @ORM\Column(type="datetime")
     */
    protected $startDate;
	
	/**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $endDate;

	/**
     * @ORM\ManyToOne(targetEntity="Team")
	 * @ORM\JoinColumn(onDelete="SET NULL")
     **/
	protected $team;
	
	/**
     * @ORM\ManyToOne(targetEntity="Position")
	 * @ORM\JoinColumn(name="position_id", referencedColumnName="id", onDelete="SET NULL")
     **/
	protected $position;
	
	
	
	public function __toString(){
		return (string)$this->getId();
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
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return WorkHistory
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return WorkHistory
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return WorkHistory
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set team
     *
     * @param \AppBundle\Entity\Team $team
     * @return WorkHistory
     */
    public function setTeam(\AppBundle\Entity\Team $team = null)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get team
     *
     * @return \AppBundle\Entity\Team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set position
     *
     * @param \AppBundle\Entity\Position $position
     * @return WorkHistory
     */
    public function setPosition(\AppBundle\Entity\Position $position = null)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return \AppBundle\Entity\Position
     */
    public function getPosition()
    {
        return $this->position;
    }
}

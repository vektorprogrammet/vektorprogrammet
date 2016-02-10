<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Table(name="AssistantHistory")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\AssistantHistoryRepository")
 */
class AssistantHistory {
	
	 /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
	protected $id;
	
	/**
     * @ORM\ManyToOne(targetEntity="User")
     **/
    protected $user;
	
	/**
     * @ORM\ManyToOne(targetEntity="Semester")
	 * @ORM\JoinColumn(onDelete="SET NULL")
     **/
	protected $semester;
	
	/**
     * @ORM\ManyToOne(targetEntity="School")
	 * @ORM\JoinColumn(onDelete="SET NULL")
     **/
	protected $school;
	
	/**
     * @ORM\Column(type="string")
     */
	protected $workdays;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $bolk;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $day;
	

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return AssistantHistory
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
     * Set semester
     *
     * @param \AppBundle\Entity\Semester $semester
     * @return AssistantHistory
     */
    public function setSemester(\AppBundle\Entity\Semester $semester = null)
    {
        $this->semester = $semester;

        return $this;
    }

    /**
     * Get semester
     *
     * @return \AppBundle\Entity\Semester
     */
    public function getSemester()
    {
        return $this->semester;
    }

    /**
     * Set school
     *
     * @param \AppBundle\Entity\School $school
     * @return AssistantHistory
     */
    public function setSchool(\AppBundle\Entity\School $school = null)
    {
        $this->school = $school;

        return $this;
    }

    /**
     * Get school
     *
     * @return \AppBundle\Entity\School
     */
    public function getSchool()
    {
        return $this->school;
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
	
	public function __toString(){
		return (string)$this->getId();
	}

    /**
     * Set workdays
     *
     * @param string $workdays
     * @return AssistantHistory
     */
    public function setWorkdays($workdays)
    {
        $this->workdays = $workdays;

        return $this;
    }

    /**
     * Get workdays
     *
     * @return string 
     */
    public function getWorkdays()
    {
        return $this->workdays;
    }

    /**
     * @return mixed
     */
    public function getBolk()
    {
        return $this->bolk;
    }

    /**
     * @param mixed $bolk
     */
    public function setBolk($bolk)
    {
        $this->bolk = $bolk;
    }

    /**
     * @return string
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @param string $day
     */
    public function setDay($day)
    {
        $this->day = $day;
    }




	
	// Used for unit testing 
	public function fromArray($data = array())
    {
        foreach ($data as $property => $value) {
            $method = "set{$property}";
            $this->$method($value);
        }
    }


}

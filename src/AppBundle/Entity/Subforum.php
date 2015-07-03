<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @ORM\Table(name="Subforum")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\SubforumRepository")
 */
class Subforum {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
	
	/**
     * @ORM\Column(type="string")
     */
    protected $name;
	
	
	// This is the owning side 
	/**
     * @ORM\ManyToMany(targetEntity="School", inversedBy="subforums")
     * @ORM\JoinTable(name="school_subforum")
     **/
	protected $schools;
	
	// This is the owning side 
	/**
     * @ORM\ManyToMany(targetEntity="Team", inversedBy="subforums")
     * @ORM\JoinTable(name="team_subforum")
     **/
	protected $teams;
	
		
	/**
     * @ORM\Column(type="string")
     */
	protected $type;
	
	/**
     * @ORM\ManyToMany(targetEntity="Forum", mappedBy="subforums")
     **/
	protected $forums;
	
	/**
     * @ORM\OneToMany(targetEntity="Thread", mappedBy="subforum", cascade={"remove"})
     **/
	protected $threads;
	
	/**
     * @ORM\Column(type="text", nullable=true)
     */
	protected $schoolDocument;
	
	
	public function __construct() {
        $this->forums = new \Doctrine\Common\Collections\ArrayCollection();
		$this->schools = new \Doctrine\Common\Collections\ArrayCollection();
		$this->threads = new \Doctrine\Common\Collections\ArrayCollection();
		$this->teams = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Subforum
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Add schools
     *
     * @param \AppBundle\Entity\School $schools
     * @return Subforum
     */
    public function addSchool(\AppBundle\Entity\School $schools)
    {
        $this->schools[] = $schools;

        return $this;
    }

    /**
     * Remove schools
     *
     * @param \AppBundle\Entity\School $schools
     */
    public function removeSchool(\AppBundle\Entity\School $schools)
    {
        $this->schools->removeElement($schools);
    }

    /**
     * Get schools
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSchools()
    {
        return $this->schools;
    }

    /**
     * Add forums
     *
     * @param \AppBundle\Entity\Forum $forums
     * @return Subforum
     */
    public function addForum(\AppBundle\Entity\Forum $forums)
    {
        $this->forums[] = $forums;

        return $this;
    }

    /**
     * Remove forums
     *
     * @param \AppBundle\Entity\Forum $forums
     */
    public function removeForum(\AppBundle\Entity\Forum $forums)
    {
        $this->forums->removeElement($forums);
    }

    /**
     * Get forums
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getForums()
    {
        return $this->forums;
    }
	
	public function __toString(){
		return $this->getName();
	}

    /**
     * Set schoolDocument
     *
     * @param string $schoolDocument
     * @return Subforum
     */
    public function setSchoolDocument($schoolDocument)
    {
        $this->schoolDocument = $schoolDocument;

        return $this;
    }

    /**
     * Get schoolDocument
     *
     * @return string 
     */
    public function getSchoolDocument()
    {
        return $this->schoolDocument;
    }

    /**
     * Add teams
     *
     * @param \AppBundle\Entity\Team $teams
     * @return Subforum
     */
    public function addTeam(\AppBundle\Entity\Team $teams)
    {
        $this->teams[] = $teams;

        return $this;
    }

    /**
     * Remove teams
     *
     * @param \AppBundle\Entity\Team $teams
     */
    public function removeTeam(\AppBundle\Entity\Team $teams)
    {
        $this->teams->removeElement($teams);
    }

    /**
     * Get teams
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTeams()
    {
        return $this->teams;
    }

    /**
     * Add threads
     *
     * @param \AppBundle\Entity\Thread $threads
     * @return Subforum
     */
    public function addThread(\AppBundle\Entity\Thread $threads)
    {
        $this->threads[] = $threads;

        return $this;
    }

    /**
     * Remove threads
     *
     * @param \AppBundle\Entity\Thread $threads
     */
    public function removeThread(\AppBundle\Entity\Thread $threads)
    {
        $this->threads->removeElement($threads);
    }

    /**
     * Get threads
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getThreads()
    {
        return $this->threads;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Subforum
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }
}

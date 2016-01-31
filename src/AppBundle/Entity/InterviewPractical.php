<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="interview_practical")
 */
class InterviewPractical
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="array")
     */
    protected $position;

    /**
     * @ORM\Column(type="string")
     */
    protected $monday;

    /**
     * @ORM\Column(type="string")
     */
    protected $tuesday;

    /**
     * @ORM\Column(type="string")
     */
    protected $wednesday;

    /**
     * @ORM\Column(type="string")
     */
    protected $thursday;

    /**
     * @ORM\Column(type="string")
     */
    protected $friday;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $substitute;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $english;

    /**
     * @ORM\Column(type="array")
     */
    protected $heardAboutFrom;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $comment;



    /**
     * @ORM\OneToOne(targetEntity="ApplicationStatistic", inversedBy="interviewPractical")
     * @ORM\JoinColumn(name="application_statistic_id", referencedColumnName="id")
     */
    protected $applicationStatistic;

    /**
     * @ORM\Column(type="boolean")
     */
    private $doublePosition;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $preferredGroup;

    /**
     * InterviewPractical constructor.
     */
    public function __construct()
    {
        $this->substitute = false;
        $this->doublePosition = false;
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
     * Set position
     *
     * @param string $position
     * @return InterviewPractical
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return string 
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set monday
     *
     * @param string $monday
     * @return InterviewPractical
     */
    public function setMonday($monday)
    {
        $this->monday = $monday;

        return $this;
    }

    /**
     * Get monday
     *
     * @return string 
     */
    public function getMonday()
    {
        return $this->monday;
    }

    /**
     * Set tuesday
     *
     * @param string $tuesday
     * @return InterviewPractical
     */
    public function setTuesday($tuesday)
    {
        $this->tuesday = $tuesday;

        return $this;
    }

    /**
     * Get tuesday
     *
     * @return string 
     */
    public function getTuesday()
    {
        return $this->tuesday;
    }

    /**
     * Set wednesday
     *
     * @param string $wednesday
     * @return InterviewPractical
     */
    public function setWednesday($wednesday)
    {
        $this->wednesday = $wednesday;

        return $this;
    }

    /**
     * Get wednesday
     *
     * @return string 
     */
    public function getWednesday()
    {
        return $this->wednesday;
    }

    /**
     * Set thursday
     *
     * @param string $thursday
     * @return InterviewPractical
     */
    public function setThursday($thursday)
    {
        $this->thursday = $thursday;

        return $this;
    }

    /**
     * Get thursday
     *
     * @return string 
     */
    public function getThursday()
    {
        return $this->thursday;
    }

    /**
     * Set friday
     *
     * @param string $friday
     * @return InterviewPractical
     */
    public function setFriday($friday)
    {
        $this->friday = $friday;

        return $this;
    }

    /**
     * Get friday
     *
     * @return string 
     */
    public function getFriday()
    {
        return $this->friday;
    }

    /**
     * Set substitute
     *
     * @param boolean $substitute
     * @return InterviewPractical
     */
    public function setSubstitute($substitute)
    {
        $this->substitute = $substitute;

        return $this;
    }

    /**
     * Get substitute
     *
     * @return boolean 
     */
    public function getSubstitute()
    {
        return $this->substitute;
    }

    /**
     * Set english
     *
     * @param boolean $english
     * @return InterviewPractical
     */
    public function setEnglish($english)
    {
        $this->english = $english;

        return $this;
    }

    /**
     * Get english
     *
     * @return boolean 
     */
    public function getEnglish()
    {
        return $this->english;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return InterviewPractical
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set heardAboutFrom
     *
     * @param string $heardAboutFrom
     * @return InterviewPractical
     */
    public function setHeardAboutFrom($heardAboutFrom)
    {
        $this->heardAboutFrom = $heardAboutFrom;

        return $this;
    }

    /**
     * Get heardAboutFrom
     *
     * @return string 
     */
    public function getHeardAboutFrom()
    {
        return $this->heardAboutFrom;
    }

    /**
     * Set applicationStatistic
     *
     * @param \AppBundle\Entity\ApplicationStatistic $applicationStatistic
     * @return InterviewPractical
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
	
	// Used for unit testing 
	public function fromArray($data = array())
    {
        foreach ($data as $property => $value) {
            $method = "set{$property}";
            $this->$method($value);
        }
    }

    /**
     * @return mixed
     */
    public function getDoublePosition()
    {
        return $this->doublePosition;
    }

    /**
     * @param mixed $doublePosition
     */
    public function setDoublePosition($doublePosition)
    {
        $this->doublePosition = $doublePosition;
    }

    /**
     * @return mixed
     */
    public function getPreferredGroup()
    {
        return $this->preferredGroup;
    }

    /**
     * @param mixed $preferredGroup
     */
    public function setPreferredGroup($preferredGroup)
    {
        $this->preferredGroup = $preferredGroup;
    }
	
}

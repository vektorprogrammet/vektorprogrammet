<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\AdmissionRepository")
 * @ORM\Table(name="ApplicationStatistic")
 */
class ApplicationStatistic {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
	
	/**
     * @ORM\Column(type="integer")
     */
    protected $gender;
	
	/**
     * @ORM\Column(type="boolean", length=250)
     */
    protected $previousParticipation;
	
	/**
     * @ORM\Column(type="boolean")
     */
    protected $accepted;

	
	/**
     * @ORM\Column(type="string" , length=20)
     */
    protected $yearOfStudy;
	
	/**
     * @ORM\ManyToOne(targetEntity="FieldOfStudy")
     **/
    protected $fieldOfStudy;
	
	/**
     * @ORM\ManyToOne(targetEntity="Semester")
     **/
    protected $semester;

    /**
     * @ORM\OneToOne(targetEntity="InterviewScore", mappedBy="applicationStatistic")
     */
    protected $interviewScore;

    /**
     * @ORM\OneToOne(targetEntity="InterviewPractical", mappedBy="applicationStatistic")
     */
    protected $interviewPractical;

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
     * Set gender
     *
     * @param boolean $gender
     * @return ApplicationStatistic
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return boolean 
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set previousParticipation
     *
     * @param boolean $previousParticipation
     * @return ApplicationStatistic
     */
    public function setPreviousParticipation($previousParticipation)
    {
        $this->previousParticipation = $previousParticipation;

        return $this;
    }

    /**
     * Get previousParticipation
     *
     * @return boolean 
     */
    public function getPreviousParticipation()
    {
        return $this->previousParticipation;
    }

    /**
     * Set accepted
     *
     * @param boolean $accepted
     * @return ApplicationStatistic
     */
    public function setAccepted($accepted)
    {
        $this->accepted = $accepted;

        return $this;
    }

    /**
     * Get accepted
     *
     * @return boolean 
     */
    public function getAccepted()
    {
        return $this->accepted;
    }

    /**
     * Set yearOfStudy
     *
     * @param string $yearOfStudy
     * @return ApplicationStatistic
     */
    public function setYearOfStudy($yearOfStudy)
    {
        $this->yearOfStudy = $yearOfStudy;

        return $this;
    }

    /**
     * Get yearOfStudy
     *
     * @return string 
     */
    public function getYearOfStudy()
    {
        return $this->yearOfStudy;
    }

    /**
     * Set fieldOfStudy
     *
     * @param \AppBundle\Entity\FieldOfStudy $fieldOfStudy
     * @return ApplicationStatistic
     */
    public function setFieldOfStudy(\AppBundle\Entity\FieldOfStudy $fieldOfStudy = null)
    {
        $this->fieldOfStudy = $fieldOfStudy;

        return $this;
    }

    /**
     * Get fieldOfStudy
     *
     * @return \AppBundle\Entity\FieldOfStudy
     */
    public function getFieldOfStudy()
    {
        return $this->fieldOfStudy;
    }

    /**
     * Set semester
     *
     * @param \AppBundle\Entity\Semester $semester
     * @return ApplicationStatistic
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
     * Set interviewScore
     *
     * @param \AppBundle\Entity\InterviewScore $interviewScore
     * @return ApplicationStatistic
     */
    public function setInterviewScore(\AppBundle\Entity\InterviewScore $interviewScore = null)
    {
        $this->interviewScore = $interviewScore;

        return $this;
    }

    /**
     * Get interviewScore
     *
     * @return \AppBundle\Entity\InterviewScore
     */
    public function getInterviewScore()
    {
        return $this->interviewScore;
    }

    /**
     * Set interviewPractical
     *
     * @param \AppBundle\Entity\InterviewPractical $interviewPractical
     * @return ApplicationStatistic
     */
    public function setInterviewPractical(\AppBundle\Entity\InterviewPractical $interviewPractical = null)
    {
        $this->interviewPractical = $interviewPractical;

        return $this;
    }

    /**
     * Get interviewPractical
     *
     * @return \AppBundle\Entity\InterviewPractical 
     */
    public function getInterviewPractical()
    {
        return $this->interviewPractical;
    }
}

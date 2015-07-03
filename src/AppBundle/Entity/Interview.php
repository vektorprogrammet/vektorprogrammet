<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="interview")
 */
class Interview
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $interviewed;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $scheduled;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $conducted;

    /**
     * @ORM\ManyToOne(targetEntity="InterviewSchema")
     * @ORM\JoinColumn(name="schema_id", referencedColumnName="id")
     */
    protected $interviewSchema; // Bidirectional, may turn out to be unidirectional

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="interviewer_id", referencedColumnName="id")
     */
    protected $interviewer; // Unidirectional, may turn out to be bidirectional

    /**
     * @ORM\OneToOne(targetEntity="Application", inversedBy="interview")
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id")
     */
    protected $application;

    /**
     * @ORM\OneToMany(targetEntity="InterviewAnswer", mappedBy="interview", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $interviewAnswers;

    /**
     * @ORM\OneToOne(targetEntity="InterviewScore", cascade={"persist"})
     * @ORM\JoinColumn(name="interview_score_id", referencedColumnName="id")
     */
    protected $interviewScore;

    /**
     * @ORM\OneToOne(targetEntity="InterviewPractical", cascade={"persist"})
     * @ORM\JoinColumn(name="interview_practical_id", referencedColumnName="id")
     */
    protected $interviewPractical;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->interviewAnswers = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set interviewSchema
     *
     * @param \AppBundle\Entity\InterviewSchema $interviewSchema
     * @return Interview
     */
    public function setInterviewSchema(\AppBundle\Entity\InterviewSchema $interviewSchema = null)
    {
        $this->interviewSchema = $interviewSchema;

        return $this;
    }

    /**
     * Get interviewSchema
     *
     * @return \AppBundle\Entity\InterviewSchema
     */
    public function getInterviewSchema()
    {
        return $this->interviewSchema;
    }

    /**
     * Set interviewer
     *
     * @param \AppBundle\Entity\User $interviewer
     * @return Interview
     */
    public function setInterviewer(\AppBundle\Entity\User $interviewer = null)
    {
        $this->interviewer = $interviewer;

        return $this;
    }

    /**
     * Get interviewer
     *
     * @return \AppBundle\Entity\User
     */
    public function getInterviewer()
    {
        return $this->interviewer;
    }

    /**
     * Set application
     *
     * @param \AppBundle\Entity\Application $application
     * @return Interview
     */
    public function setApplication(\AppBundle\Entity\Application $application = null)
    {
        $this->application = $application;

        return $this;
    }

    /**
     * Get application
     *
     * @return \AppBundle\Entity\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Add interviewAnswers
     *
     * @param \AppBundle\Entity\InterviewAnswer $interviewAnswers
     * @return Interview
     */
    public function addInterviewAnswer(\AppBundle\Entity\InterviewAnswer $interviewAnswers)
    {
        $this->interviewAnswers[] = $interviewAnswers;

        return $this;
    }

    /**
     * Remove interviewAnswers
     *
     * @param \AppBundle\Entity\InterviewAnswer $interviewAnswers
     */
    public function removeInterviewAnswer(\AppBundle\Entity\InterviewAnswer $interviewAnswers)
    {
        $this->interviewAnswers->removeElement($interviewAnswers);
    }

    /**
     * Get interviewAnswers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInterviewAnswers()
    {
        return $this->interviewAnswers;
    }

    /**
     * Set interviewScore
     *
     * @param \AppBundle\Entity\InterviewScore $interviewScore
     * @return Interview
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
     * Set interviewed
     *
     * @param boolean $interviewed
     * @return Interview
     */
    public function setInterviewed($interviewed)
    {
        $this->interviewed = $interviewed;

        return $this;
    }

    /**
     * Get interviewed
     *
     * @return boolean 
     */
    public function getInterviewed()
    {
        return $this->interviewed;
    }

    /**
     * Is the given User the interviewer of this Interview?
     *
     * @param User $user
     * @return boolean
     */
    public function isInterviewer(User $user = null)
    {
        return $user && $user->getId() == $this->getInterviewer()->getId();
    }

    /**
     * Set interviewPractical
     *
     * @param \AppBundle\Entity\InterviewPractical $interviewPractical
     * @return Interview
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

    /**
     * Set scheduled
     *
     * @param \DateTime $scheduled
     * @return Interview
     */
    public function setScheduled($scheduled)
    {
        $this->scheduled = $scheduled;

        return $this;
    }

    /**
     * Get scheduled
     *
     * @return \DateTime 
     */
    public function getScheduled()
    {
        return $this->scheduled;
    }

    /**
     * Set conducted
     *
     * @param \DateTime $conducted
     * @return Interview
     */
    public function setConducted($conducted)
    {
        $this->conducted = $conducted;

        return $this;
    }

    /**
     * Get conducted
     *
     * @return \DateTime 
     */
    public function getConducted()
    {
        return $this->conducted;
    }
}

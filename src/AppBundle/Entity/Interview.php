<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\InterviewRepository")
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
     * @ORM\Column(type="boolean")
     */
    private $cancelled;

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
     * @ORM\OneToMany(targetEntity="InterviewAnswer", mappedBy="interview", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Assert\Valid
     */
    protected $interviewAnswers;

    /**
     * @ORM\OneToOne(targetEntity="InterviewScore", cascade={"persist"})
     * @ORM\JoinColumn(name="interview_score_id", referencedColumnName="id")
     * @Assert\Valid
     */
    protected $interviewScore;

    /**
     * @ORM\ManyToOne(targetEntity="User", cascade={"persist"})
     */
    protected $user;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->interviewAnswers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->interviewed = false;
        $this->cancelled = false;
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
     * @return boolean
     */
    public function getCancelled()
    {
        return $this->cancelled;
    }

    /**
     * @param boolean $cancelled
     */
    public function setCancelled($cancelled)
    {
        $this->cancelled = $cancelled;
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

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
}

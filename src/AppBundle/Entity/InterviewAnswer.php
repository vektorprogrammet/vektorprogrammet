<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="interview_answer")
 */
class InterviewAnswer
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Interview", inversedBy="interviewAnswers")
     * @ORM\JoinColumn(name="interview_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $interview;

    /**
     * @ORM\ManyToOne(targetEntity="InterviewQuestion")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     */
    protected $interviewQuestion;

    /**
     * @ORM\Column(type="array", nullable=true)
     * @Assert\NotBlank(groups={"interview"}, message="Dette feltet kan ikke være tomt.")
     */
    protected $answer;

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
     * Set answer
     *
     * @param string $answer
     * @return InterviewAnswer
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer
     *
     * @return string 
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Set interview
     *
     * @param \AppBundle\Entity\Interview $interview
     * @return InterviewAnswer
     */
    public function setInterview(\AppBundle\Entity\Interview $interview = null)
    {
        $this->interview = $interview;

        return $this;
    }

    /**
     * Get interview
     *
     * @return \AppBundle\Entity\Interview
     */
    public function getInterview()
    {
        return $this->interview;
    }

    /**
     * Set interviewQuestion
     *
     * @param \AppBundle\Entity\InterviewQuestion $interviewQuestion
     * @return InterviewAnswer
     */
    public function setInterviewQuestion(\AppBundle\Entity\InterviewQuestion $interviewQuestion = null)
    {
        $this->interviewQuestion = $interviewQuestion;

        return $this;
    }

    /**
     * Get interviewQuestion
     *
     * @return \AppBundle\Entity\InterviewQuestion
     */
    public function getInterviewQuestion()
    {
        return $this->interviewQuestion;
    }
}

<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Validator\Constraints as CustomAssert;

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
     * @CustomAssert\InterviewAnswer(groups={"interview"})
     */
    protected $answer;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set answer.
     *
     * @param string $answer
     *
     * @return InterviewAnswer
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer.
     *
     * @return string
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Set interview.
     *
     * @param Interview $interview
     *
     * @return InterviewAnswer
     */
    public function setInterview(Interview $interview = null)
    {
        $this->interview = $interview;

        return $this;
    }

    /**
     * Get interview.
     *
     * @return Interview
     */
    public function getInterview()
    {
        return $this->interview;
    }

    /**
     * Set interviewQuestion.
     *
     * @param InterviewQuestion $interviewQuestion
     *
     * @return InterviewAnswer
     */
    public function setInterviewQuestion(InterviewQuestion $interviewQuestion = null)
    {
        $this->interviewQuestion = $interviewQuestion;

        return $this;
    }

    /**
     * Get interviewQuestion.
     *
     * @return InterviewQuestion
     */
    public function getInterviewQuestion()
    {
        return $this->interviewQuestion;
    }

    public function __toString()
    {
        if (is_string($this->answer)) {
            return $this->answer;
        }
        if (!is_array($this->answer)) {
            return "";
        }

        $answerString = "";
        foreach ($this->answer as $a) {
            if (!empty($answerString)) {
                $answerString .= ", ";
            }
            $answerString .= $a;
        }

        return $answerString;
    }
}

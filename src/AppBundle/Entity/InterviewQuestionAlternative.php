<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="interview_question_alternative")
 */
class InterviewQuestionAlternative
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Alternativ: Dette feltet kan ikke være tomt.")
     */
    protected $alternative;

    /**
     * @ORM\ManyToOne(targetEntity="InterviewQuestion", inversedBy="alternatives")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     */
    protected $interviewQuestion;

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
     * Set alternative.
     *
     * @param string $alternative
     *
     * @return InterviewQuestionAlternative
     */
    public function setAlternative($alternative)
    {
        $this->alternative = $alternative;

        return $this;
    }

    /**
     * Get alternative.
     *
     * @return string
     */
    public function getAlternative()
    {
        return $this->alternative;
    }

    /**
     * Set interviewQuestion.
     *
     * @param \AppBundle\Entity\InterviewQuestion $interviewQuestion
     *
     * @return InterviewQuestionAlternative
     */
    public function setInterviewQuestion(\AppBundle\Entity\InterviewQuestion $interviewQuestion = null)
    {
        $this->interviewQuestion = $interviewQuestion;

        return $this;
    }

    /**
     * Get interviewQuestion.
     *
     * @return \AppBundle\Entity\InterviewQuestion
     */
    public function getInterviewQuestion()
    {
        return $this->interviewQuestion;
    }
}

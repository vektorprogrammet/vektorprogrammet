<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="survey_answer")
 */
class SurveyAnswer
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="SurveyQuestion", inversedBy="answers")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     */
    protected $surveyQuestion;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $answer;

    /**
     * @ORM\ManyToOne(targetEntity="SurveyTaken", inversedBy="surveyAnswers")
     * @ORM\JoinColumn(name="survey_taken_id", referencedColumnName="id")
     */
    protected $surveyTaken;

    /**
     * @return mixed
     */
    public function getSurveyTaken()
    {
        return $this->surveyTaken;
    }

    /**
     * @param mixed $surveyTaken
     */
    public function setSurveyTaken($surveyTaken)
    {
        $this->surveyTaken = $surveyTaken;
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
     * Set answer
     *
     * @param string $answer
     * @return SurveyAnswer
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
     * Set surveyQuestion
     *
     * @param \AppBundle\Entity\SurveyQuestion $surveyQuestion
     * @return SurveyAnswer
     */
    public function setSurveyQuestion(\AppBundle\Entity\SurveyQuestion $surveyQuestion = null)
    {
        $this->surveyQuestion = $surveyQuestion;

        return $this;
    }

    /**
     * Get surveyQuestion
     *
     * @return \AppBundle\Entity\SurveyQuestion
     */
    public function getSurveyQuestion()
    {
        return $this->surveyQuestion;
    }
}

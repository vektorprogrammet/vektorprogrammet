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
     * @ORM\ManyToOne(targetEntity="Survey", inversedBy="surveyAnswers")
     * @ORM\JoinColumn(name="survey_id", referencedColumnName="id")
     */
    protected $survey;

    /**
     * @ORM\ManyToOne(targetEntity="SurveyQuestion")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     */
    protected $surveyQuestion;

    /**
     * @ORM\Column(type="array", nullable=true)
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
     * Set survey
     *
     * @param \AppBundle\Entity\Survey $survey
     * @return SurveyAnswer
     */
    public function setSurvey(\AppBundle\Entity\Survey $survey = null)
    {
        $this->survey = $survey;

        return $this;
    }

    /**
     * Get survey
     *
     * @return \AppBundle\Entity\Survey
     */
    public function getSurvey()
    {
        return $this->survey;
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

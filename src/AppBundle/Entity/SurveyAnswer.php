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
     * @return mixed
     */
    public function getSchool()
    {
        return $this->school;
    }

    /**
     * @param mixed $school
     */
    public function setSchool($school)
    {
        $this->school = $school;
    }

    /**
     * @ORM\ManyToOne(targetEntity="School")
     * @ORM\JoinColumn(name="school_id", referencedColumnName="id")
     */
    protected $school;

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
     * @return mixed
     */
    public function getSurvey()
    {
        return $this->survey;
    }

    /**
     * @param mixed $survey
     */
    public function setSurvey($survey)
    {
        $this->survey = $survey;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Survey", inversedBy="surveyAnswers")
     * @ORM\JoinColumn(name="survey_id", referencedColumnName="id")
     */
    protected $survey;

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

<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="survey_schools_answered")
 */
class SurveySchoolAnswered
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @ORM\Version
     * @var string
     */
    protected $time;

    /**
     * @ORM\ManyToOne(targetEntity="School", cascade={"persist"})
     */
    protected $school;

    /**
     * @ORM\ManyToOne(targetEntity="Survey", cascade={"persist"})
     */
    protected $survey;

    /**
     * @ORM\OneToMany(targetEntity="SurveyAnswer", mappedBy="surveySchoolAnswered", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $surveyAnswers;

    /**
     * @return mixed
     */
    public function getSurveyAnswers()
    {
        return $this->surveyAnswers;
    }

    public function addSurveyAnswer($answer){
        $this->surveyAnswers[] = $answer;
    }

    /**
     * @param mixed $surveyAnswers
     */
    public function setSurveyAnswers($surveyAnswers)
    {
        $this->surveyAnswers = $surveyAnswers;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

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

}

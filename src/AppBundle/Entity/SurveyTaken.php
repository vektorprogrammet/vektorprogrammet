<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="survey_taken")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\SurveyTakenRepository")
 */
class SurveyTaken
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
     * @ORM\OneToMany(targetEntity="SurveyAnswer", mappedBy="surveyTaken", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $surveyAnswers;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->surveyAnswers = new \Doctrine\Common\Collections\ArrayCollection();
    }

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

    public function removeNullAnswers(){
        foreach($this->surveyAnswers as $answer){
            if($answer->getAnswer() == null){
                $this->surveyAnswers->removeElement($answer);
            }
        }
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

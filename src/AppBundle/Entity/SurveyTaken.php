<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="survey_taken")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\SurveyTakenRepository")
 */
class SurveyTaken implements  \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     *
     * @var string
     */
    protected $time;

    /**
     * @var School
     *
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
     * Constructor.
     */
    public function __construct()
    {
        $this->surveyAnswers = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return SurveyAnswer[]
     */
    public function getSurveyAnswers()
    {
        return $this->surveyAnswers;
    }

    public function addSurveyAnswer($answer)
    {
        $this->surveyAnswers[] = $answer;
    }

    public function removeNullAnswers()
    {
        foreach ($this->surveyAnswers as $answer) {
            if ($answer->getAnswer() == null) {
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
     * @return Survey
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
     * @param mixed $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *               which is a value of any type other than a resource.
     *
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        $ret = array();
        $schoolQuestion = array('question_id' => 0, 'answer' => $this->school->getName());
        $ret[] = $schoolQuestion;
        foreach ($this->surveyAnswers as $a) {
            if (!$a->getSurveyQuestion()->getOptional() && ($a->getSurveyQuestion()->getType() == 'radio' || $a->getSurveyQuestion()->getType() == 'list')) {
                $ret[] = $a;
            }
        }

        return $ret;
    }
}

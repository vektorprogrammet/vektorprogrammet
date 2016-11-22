<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="survey_taken")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\SurveyTakenRepository")
 */
class SurveyTaken implements \JsonSerializable
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
     *               which is a value of any type other than a resource
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

    public function removeEmojis()
    {
        foreach ($this->surveyAnswers as $answer) {
            $answerNoEmojis = preg_replace('/([0-9#][\x{20E3}])|[\x{00ae}\x{00a9}\x{203C}\x{2047}\x{2048}\x{2049}\x{3030}\x{303D}\x{2139}\x{2122}\x{3297}\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u', '', $answer->getAnswer());
            $answer->setAnswer($answerNoEmojis);
        }
    }
}

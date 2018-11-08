<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="survey_answer")
 */
class SurveyAnswer implements \JsonSerializable
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
     * @ORM\Column(type="array", nullable=true)
     * @Assert\NotBlank()
     *
     */
    private $answerArray;

    /**
     * @ORM\ManyToOne(targetEntity="SurveyTaken", inversedBy="surveyAnswers")
     * @ORM\JoinColumn(name="survey_taken_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $surveyTaken;

    public function __construct()
    {
        $this->answerArray = [];
    }

    /**
     * @return SurveyTaken
     */
    public function getSurveyTaken()
    {
        return $this->surveyTaken;
    }

    /**
     * @param SurveyTaken $surveyTaken
     */
    public function setSurveyTaken($surveyTaken)
    {
        $this->surveyTaken = $surveyTaken;
    }

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
     * @return SurveyAnswer
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
     * Set surveyQuestion.
     *
     * @param \AppBundle\Entity\SurveyQuestion $surveyQuestion
     *
     * @return SurveyAnswer
     */
    public function setSurveyQuestion(\AppBundle\Entity\SurveyQuestion $surveyQuestion = null)
    {
        $this->surveyQuestion = $surveyQuestion;

        return $this;
    }

    /**
     * Get surveyQuestion.
     *
     * @return \AppBundle\Entity\SurveyQuestion
     */
    public function getSurveyQuestion()
    {
        return $this->surveyQuestion;
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
        return array('question_id' => $this->surveyQuestion->getId(), 'answer' => $this->answer, 'answerArray' => $this->getAnswerArray());
    }

    /**
     * @return string[]
     */
    public function getAnswerArray()
    {
        return $this->answerArray;
    }

    /**
     * @param string[] $answerArray
     */
    public function setAnswerArray($answerArray)
    {
        $this->answerArray = $answerArray;
    }
}

<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\ManyToOne(targetEntity="SurveyTaken", inversedBy="surveyAnswers")
     * @ORM\JoinColumn(name="survey_taken_id", referencedColumnName="id", onDelete="CASCADE")
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

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize(){
        return array('question_id' => $this->surveyQuestion->getId(), 'answer' => $this->answer);
    }
}

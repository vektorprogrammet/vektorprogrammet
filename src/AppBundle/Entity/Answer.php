<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\SurveyRepository")
 * @ORM\Table(name="answer")
 */
class Answer{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     * @ORM\ManyToOne(targetEntity="survey")
     */
    protected $surveyId;

    /**
     * @ORM\Column(type="integer")
     * @ORM\OneToOne(targetEntity="QuestionQAlternative")
     * @ORM\JoinColumn(name="qAlternativeId", referencedColumnName="qAlternativeId")
     */
    protected $qAlternativeId;

    /**
     * @ORM\Column(type="integer")
     * @ORM\OneToOne(targetEntity="QuestionQAlternative")
     * @ORM\JoinColumn(name="questionId", referencedColumnName="questionId")
     */
    protected $questionId;

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
    public function getSurveyId()
    {
        return $this->surveyId;
    }

    /**
     * @return mixed
     */
    public function getQAlternativeId()
    {
        return $this->qAlternativeId;
    }

    /**
     * @return mixed
     */
    public function getQuestionId()
    {
        return $this->questionId;
    }



    /**
     * Set surveyId
     *
     * @param integer $surveyId
     * @return Answer
     */
    public function setSurveyId($surveyId)
    {
        $this->surveyId = $surveyId;

        return $this;
    }

    /**
     * Set qAlternativeId
     *
     * @param integer $qAlternativeId
     * @return Answer
     */
    public function setQAlternativeId($qAlternativeId)
    {
        $this->qAlternativeId = $qAlternativeId;

        return $this;
    }

    /**
     * Set questionId
     *
     * @param integer $questionId
     * @return Answer
     */
    public function setQuestionId($questionId)
    {
        $this->questionId = $questionId;

        return $this;
    }
}

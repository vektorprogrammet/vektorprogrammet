<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="survey")
 */
class Survey implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Semester")
     * @Assert\Valid
     */
    protected $semester;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Dette feletet kan ikke være tomt.")
     */
    protected $name;

    /**
     * @var SurveyQuestion[]
     *
     * @ORM\ManyToMany(targetEntity="SurveyQuestion", cascade={"persist"})
     * @ORM\JoinTable(name="survey_surveys_questions",
     *      joinColumns={@ORM\JoinColumn(name="survey_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="question_id", referencedColumnName="id")}
     *      )
     * @Assert\Valid
     **/
    protected $surveyQuestions;

    protected $totalAnswered;

    /**
     * @return int
     */
    public function getTotalAnswered()
    {
        return $this->totalAnswered;
    }

    /**
     * @param int $totalAnswered
     */
    public function setTotalAnswered($totalAnswered)
    {
        $this->totalAnswered = $totalAnswered;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param Semester $semester
     */
    public function setSemester($semester)
    {
        $this->semester = $semester;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Semester
     */
    public function getSemester()
    {
        return $this->semester;
    }

    /**
     * @return SurveyQuestion[]
     */
    public function getSurveyQuestions()
    {
        return $this->surveyQuestions;
    }

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->surveyQuestions = new \Doctrine\Common\Collections\ArrayCollection();
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

    public function __clone()
    {
        $this->id = null;
        $this->semester = null;
        $this->surveyQuestions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->totalAnswered = 0;
    }

    public function addSurveyQuestion(\AppBundle\Entity\SurveyQuestion $surveyQuestion)
    {
        $this->surveyQuestions[] = $surveyQuestion;

        return $this;
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
        $ret = array('questions' => array());
        foreach ($this->surveyQuestions as $q) {
            if (!$q->getOptional() && ($q->getType() == 'radio' || $q->getType() == 'list')) {
                $ret['questions'][] = $q;
            }
        }

        return $ret;
    }

    public function getTextAnswerResults(): array
    {
        $textQuestionArray = array();
        $textQAarray = array();

        // Get all text questions
        foreach ($this->getSurveyQuestions() as $question) {
            if ($question->getType() == 'text') {
                $textQuestionArray[] = $question;
            }
        }

        //Collect text answers
        foreach ($textQuestionArray as $textQuestion) {
            $questionText = $textQuestion->getQuestion();
            $textQAarray[$questionText] = array();
            foreach ($textQuestion->getAnswers() as $answer) {
                $textQAarray[$questionText][] = $answer->getAnswer();
            }
        }

        return $textQAarray;
    }

    public function copy(): Survey
    {
        $surveyClone = clone $this;

        foreach ($this->getSurveyQuestions() as $question) {
            $questionClone = clone $question;
            foreach ($question->getAlternatives() as $alternative) {
                $alternativeClone = clone $alternative;
                $questionClone->addAlternative($alternativeClone);
                $alternativeClone->setSurveyQuestion($questionClone);
            }
            $surveyClone->addSurveyQuestion($questionClone);
        }

        return $surveyClone;
    }
}

<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="survey")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\SurveyRepository")
 *
 */
class Survey implements \JsonSerializable
{
    public static $SCHOOL_SURVEY = 0;
    public static $TEAM_SURVEY = 1;
    public static $ASSISTANT_SURVEY = 2;


    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Semester")
     * @Assert\Valid
     */
    private $semester;

    /**
     * @var Department
     * @ORM\ManyToOne(targetEntity="Department")
     * @Assert\Valid
     */
    private $department;


    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     */
    private $name;


    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $showCustomPopUpMessage;


    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $finishPageContent;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false, options={"default" : false})
     * @Assert\NotNull(message="Dette feltet kan ikke være tomt.")
     */
    private $confidential;

    /**
     * @var SurveyTaken[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\SurveyTaken", mappedBy="survey")
     *
     */
    private $surveysTaken;


    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false, options={"default" : 0})
     *
     */
    private $targetAudience;


    /**
     * @var string
     * @ORM\Column(type="text", nullable=false, options={"default" : "Svar på undersøkelse!"})
     */
    private $surveyPopUpMessage;

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
    private $surveyQuestions;

    private $totalAnswered;

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
     * @return Department
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * @param Department $department
     *
     * @return Survey
     */
    public function setDepartment(?Department $department): Survey
    {
        $this->department = $department;
        return $this;
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
        $this->confidential = false;
        $this->targetAudience = 0;
        $this->surveysTaken = [];
        $this->showCustomPopUpMessage = false;
        $this->surveyPopUpMessage = "";
        $this->finishPageContent = "";
    }

    public function __toString()
    {
        $str = $this->name;
        if ($this->getDepartment()) {
            $str = $str.", ".$this->getDepartment();
        }
        return $str;
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
            // !$q->getOptional() &&
            if (($q->getType() == 'radio' || $q->getType() == 'list')) {
                $ret['questions'][] = $q;
            } elseif ($q->getType() == 'check') {
                $ret['questions'][] = $q;
            }
        }

        return $ret;
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

        $surveyClone->setTargetAudience($this->getTargetAudience());
        $surveyClone->setName("Kopi av {$surveyClone->getName()}");

        return $surveyClone;
    }


    /**
     * @return boolean
     */
    public function isShowCustomPopUpMessage()
    {
        return $this->showCustomPopUpMessage;
    }

    /**
     * @param boolean $showCustomPopUpMessage
     */
    public function setShowCustomPopUpMessage($showCustomPopUpMessage)
    {
        $this->showCustomPopUpMessage = $showCustomPopUpMessage;
    }


    /**
     * @return string
     */
    public function getFinishPageContent()
    {
        if ($this->finishPageContent === null) {
            return "Takk for svaret!";
        }

        return $this->finishPageContent;
    }

    /**
     * @param string $finishPageContent
     */
    public function setFinishPageContent($finishPageContent)
    {
        $this->finishPageContent = $finishPageContent;
    }

    /**
     * @return boolean
     */
    public function isConfidential(): bool
    {
        return $this->confidential;
    }

    /**
     * @param boolean $confidential
     */
    public function setConfidential($confidential)
    {
        $this->confidential = $confidential;
    }


    /**
     * @param int $targetAudience
     */
    public function setTargetAudience($targetAudience)
    {
        $this->targetAudience = $targetAudience;
    }

    /**
     * @return int
     */
    public function getTargetAudience() : int
    {
        return $this->targetAudience;
    }

    /**
     * @param string surveyPopUpMessage
     */
    public function setSurveyPopUpMessage(?String $message)
    {
        if ($message === null) {
            $message = "Svar på undersøkelse!";
        }

        $this->surveyPopUpMessage = $message;
    }

    /**
     * @return string
     */
    public function getSurveyPopUpMessage() : string
    {
        return $this->surveyPopUpMessage;
    }

    /**
     * @return SurveyTaken[]
     */
    public function getSurveysTaken(): array
    {
        return $this->surveysTaken;
    }

    /**
     * @param SurveyTaken[] $surveysTaken
     */
    public function setSurveysTaken(array $surveysTaken): void
    {
        $this->surveysTaken = $surveysTaken;
    }
}

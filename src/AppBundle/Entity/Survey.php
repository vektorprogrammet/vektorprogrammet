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
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     */
    protected $name;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $showCustomFinishPage;

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
     * @var bool
     * @ORM\Column(type="boolean", nullable=false, options={"default" : false})
     * @Assert\NotNull(message="Dette feltet kan ikke være tomt.")
     */
    private $teamSurvey;


    /**
     * @var string
     * @ORM\Column(type="text", nullable=true, options={"default" : "Vi har en undersøkelse klar til deg!"})
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
        $this->showCustomFinishPage = false;
        $this->confidential = false;
        $this->teamSurvey = false;
        $this->surveysTaken = [];
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
            } elseif ($q->getType() == 'check') {
                $ret['questions'][] = $q;
            }
        }

        return $ret;
    }

    public function getTextAnswerWithSchoolResults(): array
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
                if ($answer->getSurveyTaken() === null || $answer->getSurveyTaken()->getSchool() === null) {
                    continue;
                }
                $textQAarray[$questionText][] = array(
                    'answerText' => $answer->getAnswer(),
                    'schoolName' => $answer->getSurveyTaken()->getSchool()->getName()
                );
            }
        }

        return $textQAarray;
    }

    public function getTextAnswerWithTeamResults(): array
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
                if ($answer->getSurveyTaken() === null || empty($answer->getSurveyTaken()->getUser()->getTeamMemberships())) {
                    continue;
                }

                $teamNames = array();
                foreach ($answer->getSurveyTaken()->getUser()->getTeamMemberships() as $teamMembership) {
                    $teamNames[] = $teamMembership->getTeam()->getName();
                }



                $teamNames = implode(", ", $teamNames);
                $find = ',';
                $replace = ' og';
                $teamNames = strrev(preg_replace(strrev("/$find/"), strrev($replace), strrev($teamNames), 1));

                $textQAarray[$questionText][] = array(
                    'answerText' => $answer->getAnswer(),
                    'teamName' => $teamNames
                );
            }
        }

        return $textQAarray;
    }


    private function map($team)
    {
        return $team->getName();
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

        $surveyClone->setTeamSurvey($this->isTeamSurvey());


        return $surveyClone;
    }

    /**
     * @return boolean
     */
    public function isShowCustomFinishPage()
    {
        return $this->showCustomFinishPage;
    }

    /**
     * @param boolean $showCustomFinishPage
     */
    public function setShowCustomFinishPage($showCustomFinishPage)
    {
        $this->showCustomFinishPage = $showCustomFinishPage;
    }

    /**
     * @return string
     */
    public function getFinishPageContent()
    {
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
     * @param boolean $teamSurvey
     */
    public function setTeamSurvey($teamSurvey)
    {
        $this->teamSurvey = $teamSurvey;
    }

    /**
     * @return boolean
     */
    public function isTeamSurvey() : bool
    {
        return $this->teamSurvey;
    }

    /**
     * @param string surveyPopUpMessage
     */
    public function setSurveyPopUpMessage($message)
    {
        $this->surveyPopUpMessage = $message;
    }

    /**
     * @return string
     */
    public function getSurveyPopUpMessage() : string
    {
        if ($this->surveyPopUpMessage===null) {
            return "Vi har en undersøkelse til deg";
        }
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

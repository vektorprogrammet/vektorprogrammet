<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(onDelete="SET NULL", nullable=true)
     */
    protected $user;


    /**
     * @ORM\Column(type="datetime", nullable=false)
     *
     * @var \DateTime
     */
    protected $time;

    /**
     * @var School
     *
     * @ORM\ManyToOne(targetEntity="School", cascade={"persist"})
     * @Assert\NotNull(groups="schoolSpecific")
     *
     */
    protected $school;

    /**
     * @var Survey
     * @ORM\ManyToOne(targetEntity="Survey", cascade={"persist"}, inversedBy="surveysTaken")
     *
     */
    protected $survey;

    /**
     * @var SurveyAnswer[]
     * @ORM\OneToMany(targetEntity="SurveyAnswer", mappedBy="surveyTaken", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $surveyAnswers;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->surveyAnswers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->time = new \DateTime();
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
            if ($answer->getSurveyQuestion()->getType() !== 'check' && $answer->getAnswer() === null) {
                $this->surveyAnswers->removeElement($answer);
            }
        }
    }

    /**
     * @param SurveyAnswer[] $surveyAnswers
     */
    public function setSurveyAnswers($surveyAnswers)
    {
        $this->surveyAnswers = $surveyAnswers;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return School
     */
    public function getSchool()
    {
        return $this->school;
    }

    /**
     * @param School $school
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
     * @param Survey $survey
     */
    public function setSurvey($survey)
    {
        $this->survey = $survey;
    }


    /**
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
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

        if ($this->survey->getTargetAudience() === 1) {
            $semester = $this->getSurvey()->getSemester();
            $teamMemberships = $this->getUser()->getTeamMemberships();
            $teamNames = array();
            foreach ($teamMemberships as $teamMembership) {
                if (!$teamMembership->isActiveInSemester($semester)) {
                    continue;
                } elseif (!in_array($teamMembership->getTeamName(), $teamNames)) {
                    $teamNames[] = $teamMembership->getTeamName();
                }
            }
            if (empty($teamNames)) {
                $teamNames[] = "Ikke teammedlem";
            }

            $affiliationQuestion = array('question_id' => 0, 'answerArray' => $teamNames);
        } else {
            $affiliationQuestion = array('question_id' => 0, 'answerArray' => [$this->school->getName()]);
        }


        $ret[] = $affiliationQuestion;
        foreach ($this->surveyAnswers as $a) {
            //!$a->getSurveyQuestion()->getOptional() && - If optional results are not wanted
            if (($a->getSurveyQuestion()->getType() == 'radio' || $a->getSurveyQuestion()->getType() == 'list')) {
                $ret[] = $a;
            } elseif ($a->getSurveyQuestion()->getType() == 'check') {
                $ret[] = $a;
            }
        }

        return $ret;
    }
}

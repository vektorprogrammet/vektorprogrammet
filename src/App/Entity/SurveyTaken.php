<?php

namespace App\Entity;

use App\Entity\Repository\SurveyTakenRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'survey_taken')]
#[ORM\Entity(repositoryClass: SurveyTakenRepository::class)]
class SurveyTaken implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @var User
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL', nullable: true)]
    protected $user;

    /**
     * @var \DateTime
     */
    #[ORM\Column(type: 'datetime', nullable: false)]
    protected $time;

    /**
     * @var School
     */
    #[ORM\ManyToOne(targetEntity: 'School', cascade: ['persist'])]
    #[Assert\NotNull(groups: ['schoolSpecific'])]
    protected $school;

    /**
     * @var Survey
     */
    #[ORM\ManyToOne(targetEntity: 'Survey', cascade: ['persist'], inversedBy: 'surveysTaken')]
    protected $survey;

    /**
     * @var SurveyAnswer[]
     */
    #[ORM\OneToMany(targetEntity: 'SurveyAnswer', mappedBy: 'surveyTaken', cascade: ['persist', 'remove'], orphanRemoval: true)]
    protected $surveyAnswers;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->surveyAnswers = new ArrayCollection();
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
     * @see http://php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *               which is a value of any type other than a resource
     *
     * @since 5.4.0
     */
    public function jsonSerialize(): mixed
    {
        $ret = [];

        if ($this->survey->getTargetAudience() === 1) {
            $semester = $this->getSurvey()->getSemester();
            $teamMemberships = $this->getUser()->getTeamMemberships();
            $teamNames = [];
            foreach ($teamMemberships as $teamMembership) {
                if (!$teamMembership->isActiveInSemester($semester)) {
                    continue;
                } elseif (!in_array($teamMembership->getTeamName(), $teamNames)) {
                    $teamNames[] = $teamMembership->getTeamName();
                }
            }
            if (empty($teamNames)) {
                $teamNames[] = 'Ikke teammedlem';
            }

            $affiliationQuestion = ['question_id' => 0, 'answerArray' => $teamNames];
        } else {
            $affiliationQuestion = ['question_id' => 0, 'answerArray' => [$this->school->getName()]];
        }

        $ret[] = $affiliationQuestion;
        foreach ($this->surveyAnswers as $a) {
            // !$a->getSurveyQuestion()->getOptional() && - If optional results are not wanted
            if ($a->getSurveyQuestion()->getType() == 'radio' || $a->getSurveyQuestion()->getType() == 'list') {
                $ret[] = $a;
            } elseif ($a->getSurveyQuestion()->getType() == 'check') {
                $ret[] = $a;
            }
        }

        return $ret;
    }
}

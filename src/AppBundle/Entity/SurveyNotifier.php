<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="survey_notifier")
 */
class SurveyNotifier
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $name;


    /**
     * @var UserGroup
     * @ORM\ManyToOne(targetEntity="UserGroup")
     */
    private $usergroup;


    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="SurveyNotification", mappedBy="surveyNotifier")
     */
    private $surveyNotifications;


    /**
     * @var Survey
     * @ORM\ManyToOne(targetEntity="Survey")
     */
    private $survey;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $timeOfNotification;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $isEmail;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $isSMS;


    public function __construct()
    {
        $this->name ="";
        $this->isEmail = false;
        $this->isSMS = false;
        $this->surveyNotifications = array();
        $this->timeOfNotification = new \DateTime('tomorrow');

    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return UserGroup?
     */
    public function getUsergroup(): ?UserGroup
    {
        return $this->usergroup;
    }

    /**
     * @param UserGroup $usergroup
     */
    public function setUsergroup(UserGroup $usergroup): void
    {
        $this->usergroup = $usergroup;
    }

    /**
     * @return Survey?
     */
    public function getSurvey(): ?Survey
    {
        return $this->survey;
    }

    /**
     * @param Survey $survey
     */
    public function setSurvey(Survey $survey): void
    {
        $this->survey = $survey;
    }

    /**
     * @return \DateTime?
     */
    public function getTimeOfNotification(): ?\DateTime
    {
        return $this->timeOfNotification;
    }

    /**
     * @param \DateTime $timeOfNotification
     */
    public function setTimeOfNotification(\DateTime $timeOfNotification): void
    {
        $this->timeOfNotification = $timeOfNotification;
    }

    /**S
     * @return bool
     */
    public function isEmail(): bool
    {
        return $this->isEmail;
    }

    /**
     * @param bool $isEmail
     */
    public function setIsEmail(bool $isEmail): void
    {
        $this->isEmail = $isEmail;
    }

    /**
     * @return bool
     */
    public function isSMS(): bool
    {
        return $this->isSMS;
    }

    /**
     * @param bool $isSMS
     */
    public function setIsSMS(bool $isSMS): void
    {
        $this->isSMS = $isSMS;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return SurveyNotification[]
     */
    public function getSurveyNotifications(): array
    {
        return $this->surveyNotifications;
    }

    /**
     * @param SurveyNotification[] $surveyNotifications
     */
    public function setSurveyNotifications(array $surveyNotifications): void
    {
        $this->surveyNotifications = $surveyNotifications;
    }



}

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
    public static $EMAIL_NOTIFICATION = 0;
    public static $SMS_NOTIFICATION = 1;


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
     * @ORM\ManyToOne(targetEntity="UserGroup", cascade={"persist"})
     */
    private $userGroup;


    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="SurveyNotification", mappedBy="surveyNotifier", cascade={"remove"})
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
     * @var int
     * @ORM\Column(type="integer")
     */
    private $notificationType;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $isAllSent;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default"=false})
     */
    private $isActive;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $smsMessage;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $emailSubject;



    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $emailMessage;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $senderUser;



    public function __construct()
    {
        $this->name ="";
        $this->surveyNotifications = array();
        $this->timeOfNotification = new \DateTime('tomorrow');
        $this->isAllSent = false;
        $this->isActive = false;
        $this->notificationType = SurveyNotifier::$EMAIL_NOTIFICATION;
        $this->smsMessage = "Vi i vektor jobber kontinuerlig for å forbedre assistentopplevelsen, men for å kunne gjøre det er vi avhengig tilbakemelding. Svar på følgende undersøkelse og vær med i trekning av flotte premier da vel!";
        $this->emailMessage = "Vi i vektor jobber kontinuerlig for å forbedre assistentopplevelsen, men for å kunne gjøre det er vi avhengig tilbakemelding. Svar på følgende undersøkelse og vær med i trekning av flotte premier da vel!";
        $this->emailSubject = "Undersøkelse fra Vektor";


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
    public function getUserGroup(): ?UserGroup
    {
        return $this->userGroup;
    }

    /**
     * @param UserGroup $userGroup
     */
    public function setUserGroup(UserGroup $userGroup): void
    {
        $this->userGroup = $userGroup;
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
    public function getSurveyNotifications()
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

    /**
     * @return bool
     */
    public function isAllSent(): bool
    {
        return $this->isAllSent;
    }

    /**
     * @param bool $isAllSent
     */
    public function setIsAllSent(bool $isAllSent): void
    {
        $this->isAllSent = $isAllSent;
    }

    /**
     * @return int
     */
    public function getNotificationType(): int
    {
        return $this->notificationType;
    }

    /**
     * @param int $notificationType
     */
    public function setNotificationType(int $notificationType): void
    {
        $this->notificationType = $notificationType;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     */
    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    /**
     * @return User
     */
    public function getSenderUser(): User
    {
        return $this->senderUser;
    }

    /**
     * @param User $senderUser
     */
    public function setSenderUser(User $senderUser): void
    {
        $this->senderUser = $senderUser;
    }

    /**
     * @return string
     */
    public function getSmsMessage(): string
    {
        return $this->smsMessage;
    }

    /**
     * @param string $smsMessage
     */
    public function setSmsMessage(string $smsMessage): void
    {
        $this->smsMessage = $smsMessage;
    }

    /**
     * @return string
     */
    public function getEmailMessage(): string
    {
        return $this->emailMessage;
    }

    /**
     * @param string $emailMessage
     */
    public function setEmailMessage(string $emailMessage): void
    {
        $this->emailMessage = $emailMessage;
    }

    /**
     * @return string
     */
    public function getEmailSubject(): string
    {
        return $this->emailSubject;
    }

    /**
     * @param string $emailSubject
     */
    public function setEmailSubject(string $emailSubject): void
    {
        $this->emailSubject = $emailSubject;
    }



}

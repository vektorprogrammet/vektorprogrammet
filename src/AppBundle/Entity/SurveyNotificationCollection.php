<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="survey_notification_collection")
 */
class SurveyNotificationCollection
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
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="UserGroup", cascade={"persist"})
     * @Assert\NotNull
     */
    private $userGroups;


    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="SurveyNotification", mappedBy="surveyNotificationCollection", cascade={"remove"})
     */
    private $surveyNotifications;


    /**
     * @var Survey
     * @ORM\ManyToOne(targetEntity="Survey")
     * @Assert\NotBlank
     */
    private $survey;

    /**
     * @var DateTime
     * @ORM\Column(name="time_of_notification", type="datetime", nullable=false)
     */
    private $timeOfNotification;

    /**
     * @var int
     * @ORM\Column(name="notification_type", type="integer")
     */
    private $notificationType;

    /**
     * @var bool
     * @ORM\Column(name="all_sent", type="boolean")
     */
    private $allSent;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default"=false})
     */
    private $active;

    /**
     * @var string
     * @ORM\Column(name="sms_message", type="string")
     *
     */
    private $smsMessage;

    /**
     * @var string
     * @ORM\Column(name="email_from_name", nullable=false, type="string")
     */
    private $emailFromName;


    /**
     * @var string
     * @ORM\Column(name="email_subject", type="string")
     */
    private $emailSubject;


    /**
     * @var string
     * @ORM\Column(name="email_message", type="text")
     */
    private $emailMessage;

    /**
     * @var string
     * @ORM\Column(name="email_end_message", type="text")
     */
    private $emailEndMessage;

    /**
     * @var int
     * @ORM\Column(name="email_type", type="integer")
     */
    private $emailType;




    public function __construct()
    {
        $this->name ="";
        $this->surveyNotifications = array();
        $this->timeOfNotification = new DateTime('tomorrow');
        $this->allSent = false;
        $this->active = false;
        $this->notificationType = SurveyNotificationCollection::$EMAIL_NOTIFICATION;
        $this->smsMessage = "Vi i vektor jobber kontinuerlig for å forbedre assistentopplevelsen, men for å kunne gjøre det er vi avhengig tilbakemelding. Svar på følgende undersøkelse og vær med i trekning av flotte premier da vel!";
        $this->emailMessage = "Vi i vektor jobber kontinuerlig for å forbedre assistentopplevelsen, men for å kunne gjøre det er vi avhengig tilbakemelding. Svar på følgende undersøkelse og vær med i trekning av flotte premier da vel!";
        $this->emailEndMessage = "<p>Med vennlig hilsen,<br/>Vektorevaluering</p>";
        $this->emailSubject = "Undersøkelse fra Vektor";
        $this->emailType = 0;
        $this->userGroups = array();
        $this->emailFromName = "Vektorprogrammet";
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ArrayCollection
     */
    public function getUserGroups()
    {
        return $this->userGroups;
    }




    /**
     * @param UserGroup[] $userGroups
     */
    public function setUserGroups(array $userGroups): void
    {
        $this->userGroups = $userGroups;
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
     * @return DateTime?
     */
    public function getTimeOfNotification(): ?DateTime
    {
        return $this->timeOfNotification;
    }

    /**
     * @param DateTime $timeOfNotification
     */
    public function setTimeOfNotification(DateTime $timeOfNotification): void
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
        return $this->allSent;
    }

    /**
     * @param bool $allSent
     */
    public function setAllSent(bool $allSent): void
    {
        $this->allSent = $allSent;
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
        return $this->active;
    }

    /**
     * @param bool $isActive
     */
    public function setActive(bool $isActive): void
    {
        $this->active = $isActive;
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

    /**
     * @return string
     */
    public function getEmailEndMessage(): string
    {
        return $this->emailEndMessage;
    }

    /**
     * @param string $emailEndMessage
     */
    public function setEmailEndMessage(string $emailEndMessage): void
    {
        $this->emailEndMessage = $emailEndMessage;
    }

    /**
     * @return int
     */
    public function getEmailType(): int
    {
        return $this->emailType;
    }

    /**
     * @param int $emailType
     */
    public function setEmailType(int $emailType): void
    {
        $this->emailType = $emailType;
    }

    /**
     * @return string
     */
    public function getEmailFromName(): string
    {
        return $this->emailFromName;
    }

    /**
     * @param string $emailFromName
     */
    public function setEmailFromName(string $emailFromName): void
    {
        $this->emailFromName = $emailFromName;
    }
}

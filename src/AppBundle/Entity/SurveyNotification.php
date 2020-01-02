<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\SurveyNotificationRepository")
 * @ORM\Table(name="survey_notification")
 * @UniqueEntity(
 *      fields={"userIdentifier"}
 * )
 */
class SurveyNotification
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     */
    private $user;

    /**
     * @var SurveyLinkClick[]
     * @ORM\OneToMany(targetEntity="SurveyLinkClick", mappedBy="notification")
     */
    private $surveyLinkClicks;

    /**
     * @var SurveyNotificationCollection
     * @ORM\ManyToOne(targetEntity="SurveyNotificationCollection", inversedBy="surveyNotification")
     */
    private $surveyNotificationCollection;


    /**
     * @var \DateTime
     * @ORM\Column(name="time_notification_Sent", type="datetime", nullable=true)
     */
    private $timeNotificationSent;


    /**
     * @var string
     * @ORM\Column(name="user_identifier", type="string", unique=true)
     */
    private $userIdentifier;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $sent;


    public function __construct()
    {
        $this->userIdentifier = bin2hex(openssl_random_pseudo_bytes(12));
        $this->sent = false;
        $this->surveyLinkClicks = array();
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

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }


    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }


    /**
     * @param \DateTime[] $surveyLinkClicks
     */
    public function setSurveyLinkClicks(array $surveyLinkClicks): void
    {
        $this->surveyLinkClicks = $surveyLinkClicks;
    }

    /**
     * @return \DateTime[]
     */
    public function getSurveyLinkClicks()
    {
        return $this->surveyLinkClicks;
    }



    /**
     * @return \DateTime?
     */
    public function getTimeNotificationSent(): ?\DateTime
    {
        return $this->timeNotificationSent;
    }

    /**
     * @param \DateTime $timeNotificationSent
     */
    public function setTimeNotificationSent(\DateTime $timeNotificationSent): void
    {
        $this->timeNotificationSent = $timeNotificationSent;
    }

    /**
     * @return SurveyNotificationCollection
     */
    public function getSurveyNotificationCollection(): SurveyNotificationCollection
    {
        return $this->surveyNotificationCollection;
    }

    /**
     * @param SurveyNotificationCollection $surveyNotificationCollection
     */
    public function setSurveyNotificationCollection(SurveyNotificationCollection $surveyNotificationCollection): void
    {
        $this->surveyNotificationCollection = $surveyNotificationCollection;
    }

    /**
     * @return string
     */
    public function getUserIdentifier(): string
    {
        return $this->userIdentifier;
    }

    /**
     * @param string $userIdentifier
     */
    public function setUserIdentifier(string $userIdentifier) : void
    {
        $this->userIdentifier = $userIdentifier;
    }

    /**
     * @return bool
     */
    public function isSent(): bool
    {
        return $this->sent;
    }

    /**
     * @param bool $sent
     */
    public function setSent(bool $sent): void
    {
        $this->sent = $sent;
    }
}

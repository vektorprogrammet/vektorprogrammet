<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\SurveyNotificationRepository")
 * @ORM\Table(name="survey_notification")
 *
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
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $timeOfFirstVisit;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $timeNotificationSent;

    /**
     * @var SurveyNotifier
     * @ORM\ManyToOne(targetEntity="SurveyNotifier", inversedBy="surveyNotifications")
     */
    private $surveyNotifier;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $userIdentifier;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $isSent;


    public function __construct()
    {
        $this->userIdentifier = bin2hex(openssl_random_pseudo_bytes(12));
        $this->isSent = false;
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
     * @param \DateTime $timeOfFirstVisit
     */
    public function setTimeOfFirstVisit(\DateTime $timeOfFirstVisit): void
    {
        $this->timeOfFirstVisit = $timeOfFirstVisit;
    }

    /**
     * @return \DateTime?
     */
    public function getTimeOfFirstVisit(): ?\DateTime
    {
        return $this->timeOfFirstVisit;
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
     * @return SurveyNotifier
     */
    public function getSurveyNotifier(): SurveyNotifier
    {
        return $this->surveyNotifier;
    }

    /**
     * @param SurveyNotifier $surveyNotifier
     */
    public function setSurveyNotifier(SurveyNotifier $surveyNotifier): void
    {
        $this->surveyNotifier = $surveyNotifier;
    }

    /**
     * @return string
     */
    public function getUserIdentifier(): string
    {
        return $this->userIdentifier;
    }

    /**
     * @return bool
     */
    public function isSent(): bool
    {
        return $this->isSent;
    }

    /**
     * @param bool $isSent
     */
    public function setIsSent(bool $isSent): void
    {
        $this->isSent = $isSent;
    }
}

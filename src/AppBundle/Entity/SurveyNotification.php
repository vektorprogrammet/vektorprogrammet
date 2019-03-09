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
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User")
     */
    private $user;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $timeOfFirstVisit;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $timeNotificationSent;

    /**
     * @var Survey
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Survey")
     */
    private $survey;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $userIdentifier;


    public function __construct()
    {
        $this->userIdentifier = bin2hex(openssl_random_pseudo_bytes(12));
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
     * @return \DateTime
     */
    public function getTimeNotificationSent(): \DateTime
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
     * @return Survey
     */
    public function getSurvey(): Survey
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
     * @return string
     */
    public function getUserIdentifier(): string
    {
        return $this->userIdentifier;
    }

}


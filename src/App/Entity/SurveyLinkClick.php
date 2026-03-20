<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'survey_link_click')]
class SurveyLinkClick
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private $id;

    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'time_of_visit', type: 'datetime')]
    private $timeOfVisit;

    /**
     * @var SurveyNotification
     */
    #[ORM\ManyToOne(targetEntity: SurveyNotification::class, inversedBy: 'surveyLinkClicks')]
    private $notification;

    public function __construct()
    {
        $this->timeOfVisit = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getTimeOfVisit(): \DateTime
    {
        return $this->timeOfVisit;
    }

    public function setTimeOfVisit(\DateTime $timeOfVisit): void
    {
        $this->timeOfVisit = $timeOfVisit;
    }

    public function getNotification(): SurveyNotification
    {
        return $this->notification;
    }

    public function setNotification(SurveyNotification $notification): void
    {
        $this->notification = $notification;
    }
}

<?php

namespace AppBundle\Service;

use AppBundle\Entity\Department;
use Symfony\Component\Routing\Router;

class InterviewNotificationManager
{
    private $slackMessenger;
    private $applicationData;
    private $router;

    /**
     * InterviewNotificationManager constructor.
     *
     * @param SlackMessenger  $slackMessenger
     * @param ApplicationData $applicationData
     * @param Router          $router
     */
    public function __construct(SlackMessenger $slackMessenger, ApplicationData $applicationData, Router $router)
    {
        $this->slackMessenger = $slackMessenger;
        $this->applicationData = $applicationData;
        $this->router = $router;
    }

    public function sendApplicationCountNotification(Department $department)
    {
        $interviewsCompletedCount = $this->applicationData->getInterviewedAssistantsCount();
        $interviewsLeftCount = $this->applicationData->getInterviewsLeftCount();

        $interviewsLink = $this->router->generate(
            'applications_show_interviewed_by_semester',
            array('id' => $department->getCurrentOrLatestSemester()->getId()),
            Router::ABSOLUTE_URL
        );

        $this->slackMessenger->notify(
            "$department har fullført *$interviewsCompletedCount* intervjuer. *$interviewsLeftCount* intervjuer gjenstår. Se alle intervjuene her: $interviewsLink"
        );
    }

    public function sendInterviewsCompletedNotification(Department $department)
    {
        $this->applicationData->setDepartment($department);

        $this->slackMessenger->notify("$department har fullført alle sine *{$this->applicationData->getTotalInterviewsCount()}* intervjuer! :tada:");

        $this->slackMessenger->notify(
            "```\n".
            "Antall søkere: {$this->applicationData->getApplicationCount()}\n".
            "Tidligere assistenter: {$this->applicationData->getPreviousParticipationCount()}\n".
            "Intervjuer fullført: {$this->applicationData->getInterviewedAssistantsCount()}\n".
            "Kansellerte intervjuer: {$this->applicationData->getCancelledInterviewsCount()}\n".
            "Kjønn: {$this->applicationData->getMaleCount()} menn ({$this->applicationData->getMalePercentage()}%), ".
            "{$this->applicationData->getFemaleCount()} damer ({$this->applicationData->getFemalePercentage()}%)\n".
            '```'
        );

        $this->slackMessenger->notify(
            'Se alle intervjuene her: '.$this->router->generate(
                'applications_show_interviewed_by_semester',
                array('id' => $department->getCurrentOrLatestSemester()->getId(), 'status' => 'interviewed'),
                Router::ABSOLUTE_URL
            )
        );
    }
}

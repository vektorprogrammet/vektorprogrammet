<?php

namespace AppBundle\Service;

use AppBundle\Entity\Application;
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

    public function sendNewApplicationNotification(Application $application)
    {
        $interviewee = $application->getUser();

        $interviewer = $application->getInterview()->getInterviewer();

        $department = $interviewer->getDepartment();

        $interviewsLeft = $this->applicationData->getInterviewsLeftCount();

        $interviewLink = $this->router->generate('interview_show', array('id' => $application->getId()), Router::ABSOLUTE_URL);

        $this->slackMessenger->notify(
            "$department: *$interviewer* har fullført et intervju med *$interviewee*. "
            ."$department har *$interviewsLeft* intervju(er) igjen. Les hele intervjuet her: $interviewLink"
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
                'admissionadmin_filter_applications_by_department',
                array('id' => $department->getId(), 'status' => 'interviewed'),
                Router::ABSOLUTE_URL
            ));
    }
}

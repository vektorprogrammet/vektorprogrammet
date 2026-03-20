<?php

namespace App\Interview\Infrastructure;

use App\Admission\Infrastructure\ApplicationData;
use App\Support\Infrastructure\Slack\SlackMessenger;
use App\Entity\Department;
use App\Shared\Entity\Semester;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;

class InterviewNotificationManager
{
    public function __construct(
        private readonly SlackMessenger $slackMessenger,
        private readonly ApplicationData $applicationData,
        private readonly RouterInterface $router,
    ) {
    }

    public function sendApplicationCountNotification(Department $department, Semester $semester)
    {
        $interviewsCompletedCount = $this->applicationData->getInterviewedAssistantsCount();
        $interviewsLeftCount = $this->applicationData->getInterviewsLeftCount();

        $interviewsLink = $this->router->generate(
            'applications_show_interviewed',
            [
                'department' => $department->getId(),
                'semester' => $semester->getId(),
            ],
            Router::ABSOLUTE_URL
        );

        $this->slackMessenger->notify(
            "$department har fullført *$interviewsCompletedCount* intervjuer. *$interviewsLeftCount* intervjuer gjenstår. Se alle intervjuene her: $interviewsLink"
        );
    }

    public function sendInterviewsCompletedNotification(Department $department, Semester $semester)
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
                'applications_show_interviewed',
                [
                    'department' => $department->getId(),
                    'semester' => $semester->getId(),
                ],
                Router::ABSOLUTE_URL
            )
        );
    }
}

<?php

namespace App\Admission\Infrastructure;

use App\Admission\Domain\Rules\ApplicationStatusRule;
use App\Admission\Domain\ValueObjects\ApplicationStatus;
use App\Admission\Infrastructure\Entity\Application;

class ApplicationManager
{
    public function __construct(
        private readonly ApplicationStatusRule $statusRule,
    ) {
    }

    public function getApplicationStatus(Application $application): ApplicationStatus
    {
        $interview = $application->getInterview();
        $user = $application->getUser();

        return $this->statusRule->determine(
            isActiveAssistant: $user->isActiveAssistant(),
            hasBeenAssistant: $user->hasBeenAssistant(),
            hasInterview: $interview !== null,
            isInterviewed: $interview?->getInterviewed() ?? false,
            interviewStatus: $interview?->getInterviewStatus(),
            interviewRoom: $interview?->getRoom(),
            interviewScheduledFormatted: $interview?->getScheduled()?->format('d. M H:i'),
        );
    }
}

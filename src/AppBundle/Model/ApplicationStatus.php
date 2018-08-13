<?php

namespace AppBundle\Model;

class ApplicationStatus
{
    private $text;
    private $nextAction;
    private $step;

    const CANCELLED = -1;
    const APPLICATION_NOT_RECEIVED = 0;
    const APPLICATION_RECEIVED = 1;
    const INVITED_TO_INTERVIEW = 2;
    const INTERVIEW_ACCEPTED = 3;
    const INTERVIEW_COMPLETED = 4;
    const ASSIGNED_TO_SCHOOL = 5;

    const APPLICATION_PROCESS = [
        "Send inn søknad",
        "Bli invitert til intervju",
        "Godta intervjutidspunkt",
        "Still til intervju",
        "Bli tatt opp som vektorassistent"
    ];

    public function __construct(int $step, string $text, string $nextAction)
    {
        $this->text       = $text;
        $this->nextAction = $nextAction;
        $this->step       = $step;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getNextAction(): string
    {
        return $this->nextAction;
    }

    public function getStep(): int
    {
        return $this->step;
    }

    public function getApplicationProcess(): array
    {
        return ApplicationStatus::APPLICATION_PROCESS;
    }
}

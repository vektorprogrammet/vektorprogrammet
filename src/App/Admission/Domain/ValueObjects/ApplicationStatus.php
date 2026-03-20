<?php

namespace App\Admission\Domain\ValueObjects;

class ApplicationStatus
{
    public const CANCELLED = -1;
    public const APPLICATION_NOT_RECEIVED = 0;
    public const APPLICATION_RECEIVED = 1;
    public const INVITED_TO_INTERVIEW = 2;
    public const INTERVIEW_ACCEPTED = 3;
    public const INTERVIEW_COMPLETED = 4;
    public const ASSIGNED_TO_SCHOOL = 5;

    public const APPLICATION_PROCESS = [
        'Send inn søknad',
        'Bli invitert til intervju',
        'Godta intervjutidspunkt',
        'Still til intervju',
        'Bli tatt opp som vektorassistent',
    ];

    public function __construct(private readonly int $step, private readonly string $text, private readonly string $nextAction)
    {
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

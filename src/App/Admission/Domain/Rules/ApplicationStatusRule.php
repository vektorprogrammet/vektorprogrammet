<?php

namespace App\Admission\Domain\Rules;

use App\Admission\Domain\ValueObjects\ApplicationStatus;

class ApplicationStatusRule
{
    // Local mirror of InterviewStatusType constants (Interview context).
    // Avoids cross-context domain import while keeping match arms self-documenting.
    private const INTERVIEW_PENDING = 0;
    private const INTERVIEW_ACCEPTED = 1;
    private const INTERVIEW_REQUEST_NEW_TIME = 2;
    private const INTERVIEW_CANCELLED = 3;
    private const INTERVIEW_NO_CONTACT = 4;

    public function determine(
        bool $isActiveAssistant,
        bool $hasBeenAssistant,
        bool $hasInterview,
        bool $isInterviewed,
        ?int $interviewStatus,
        ?string $interviewRoom,
        ?string $interviewScheduledFormatted,
    ): ApplicationStatus {
        if ($isActiveAssistant) {
            return new ApplicationStatus(
                ApplicationStatus::ASSIGNED_TO_SCHOOL,
                'Tatt opp som vektorassistent',
                'Ta kontakt med dine vektorpartnere og dra ut til skolen'
            );
        }

        if ($hasBeenAssistant) {
            return new ApplicationStatus(
                ApplicationStatus::INTERVIEW_COMPLETED,
                'Søknad mottatt',
                'Siden du har vært assistent tidligere trenger du ikke å møte på intervju. Du vil få en e-post når opptaket er klart.'
            );
        }

        if (!$hasInterview) {
            return new ApplicationStatus(
                ApplicationStatus::APPLICATION_RECEIVED,
                'Søknad mottatt',
                'Vent på å bli invitert til intervju'
            );
        }

        if ($isInterviewed) {
            return new ApplicationStatus(
                ApplicationStatus::INTERVIEW_COMPLETED,
                'Intervju gjennomført',
                'Søknaden din vurderes av Vektorprogrammet. Du vil få svar på e-post.'
            );
        }

        return match ($interviewStatus) {
            self::INTERVIEW_NO_CONTACT => new ApplicationStatus(
                ApplicationStatus::APPLICATION_RECEIVED,
                'Søknad mottatt',
                'Vent på å bli invitert til intervju'
            ),
            self::INTERVIEW_REQUEST_NEW_TIME => new ApplicationStatus(
                ApplicationStatus::APPLICATION_RECEIVED,
                'Endring av tidspunkt til intervju',
                'Vent på å få et nytt tidspunkt til intervju'
            ),
            self::INTERVIEW_PENDING => new ApplicationStatus(
                ApplicationStatus::INVITED_TO_INTERVIEW,
                'Invitert til intervju',
                'Godta intervjutidspunktet'
            ),
            self::INTERVIEW_ACCEPTED => new ApplicationStatus(
                ApplicationStatus::INTERVIEW_ACCEPTED,
                'Intervjutidspunkt godtatt',
                'Møt opp til intervju. Sted: '.$interviewRoom.'. Tid: '.$interviewScheduledFormatted
            ),
            self::INTERVIEW_CANCELLED => new ApplicationStatus(
                ApplicationStatus::CANCELLED,
                'Søknad kansellert',
                'Ingen videre handling er nødvendig. Du vil ikke bli tatt opp som vektorassistent.'
            ),
            default => new ApplicationStatus(
                ApplicationStatus::APPLICATION_NOT_RECEIVED,
                'Ingen søknad mottatt',
                'Send inn søknad om å bli vektorassistent'
            ),
        };
    }
}

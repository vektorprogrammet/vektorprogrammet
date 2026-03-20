<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Rules;

use App\Admission\Domain\Rules\ApplicationStatusRule;
use App\Admission\Domain\ValueObjects\ApplicationStatus;
use PHPUnit\Framework\TestCase;

class ApplicationStatusRuleTest extends TestCase
{
    private ApplicationStatusRule $rule;

    protected function setUp(): void
    {
        $this->rule = new ApplicationStatusRule();
    }

    public function testActiveAssistantReturnsAssignedToSchool(): void
    {
        $status = $this->rule->determine(true, false, false, false, null, null, null);

        $this->assertSame(ApplicationStatus::ASSIGNED_TO_SCHOOL, $status->getStep());
    }

    public function testHasBeenAssistantReturnsInterviewCompleted(): void
    {
        $status = $this->rule->determine(false, true, false, false, null, null, null);

        $this->assertSame(ApplicationStatus::INTERVIEW_COMPLETED, $status->getStep());
    }

    public function testNoInterviewReturnsApplicationReceived(): void
    {
        $status = $this->rule->determine(false, false, false, false, null, null, null);

        $this->assertSame(ApplicationStatus::APPLICATION_RECEIVED, $status->getStep());
    }

    public function testInterviewedReturnsInterviewCompleted(): void
    {
        $status = $this->rule->determine(false, false, true, true, null, null, null);

        $this->assertSame(ApplicationStatus::INTERVIEW_COMPLETED, $status->getStep());
    }

    public function testInterviewStatusPendingReturnsInvitedToInterview(): void
    {
        // Status 0 = PENDING
        $status = $this->rule->determine(false, false, true, false, 0, null, null);

        $this->assertSame(ApplicationStatus::INVITED_TO_INTERVIEW, $status->getStep());
    }

    public function testInterviewStatusAcceptedReturnsInterviewAccepted(): void
    {
        // Status 1 = ACCEPTED
        $status = $this->rule->determine(false, false, true, false, 1, 'Room 101', '2026-04-01 10:00');

        $this->assertSame(ApplicationStatus::INTERVIEW_ACCEPTED, $status->getStep());
    }

    public function testInterviewStatusRequestNewTimeReturnsApplicationReceived(): void
    {
        // Status 2 = REQUEST_NEW_TIME
        $status = $this->rule->determine(false, false, true, false, 2, null, null);

        $this->assertSame(ApplicationStatus::APPLICATION_RECEIVED, $status->getStep());
    }

    public function testInterviewStatusCancelledReturnsCancelled(): void
    {
        // Status 3 = CANCELLED
        $status = $this->rule->determine(false, false, true, false, 3, null, null);

        $this->assertSame(ApplicationStatus::CANCELLED, $status->getStep());
    }

    public function testInterviewStatusNoContactReturnsApplicationReceived(): void
    {
        // Status 4 = NO_CONTACT
        $status = $this->rule->determine(false, false, true, false, 4, null, null);

        $this->assertSame(ApplicationStatus::APPLICATION_RECEIVED, $status->getStep());
    }

    public function testUnknownInterviewStatusReturnsApplicationNotReceived(): void
    {
        // Status 99 = unknown/default
        $status = $this->rule->determine(false, false, true, false, 99, null, null);

        $this->assertSame(ApplicationStatus::APPLICATION_NOT_RECEIVED, $status->getStep());
    }

    public function testActiveAssistantTakesPrecedenceOverHasBeenAssistant(): void
    {
        $status = $this->rule->determine(true, true, false, false, null, null, null);

        $this->assertSame(ApplicationStatus::ASSIGNED_TO_SCHOOL, $status->getStep());
    }

    public function testInterviewAcceptedNextActionIncludesRoomAndTime(): void
    {
        $status = $this->rule->determine(false, false, true, false, 1, 'A101', '10:00');

        $this->assertStringContainsString('A101', $status->getNextAction());
        $this->assertStringContainsString('10:00', $status->getNextAction());
    }
}

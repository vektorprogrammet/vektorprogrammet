<?php

namespace App\Tests\App\Interview\Infrastructure\Entity;

use App\Interview\Domain\ValueObjects\InterviewStatusType;
use App\Interview\Infrastructure\Entity\Interview;
use PHPUnit\Framework\TestCase;

class InterviewCancelledTest extends TestCase
{
    public function testSetCancelledFalseOnNoContactInterviewDoesNotChangeStatus(): void
    {
        $interview = new Interview();
        // Initial status is NO_CONTACT
        $this->assertSame(InterviewStatusType::NO_CONTACT, $interview->getInterviewStatus());

        $interview->setCancelled(false);

        $this->assertSame(
            InterviewStatusType::NO_CONTACT,
            $interview->getInterviewStatus(),
            'setCancelled(false) must not silently transition status to ACCEPTED'
        );
    }

    public function testSetCancelledTrueOnPendingInterviewCancels(): void
    {
        $interview = new Interview();
        $interview->setInterviewStatus(InterviewStatusType::PENDING);

        $interview->setCancelled(true);

        $this->assertSame(InterviewStatusType::CANCELLED, $interview->getInterviewStatus());
    }
}

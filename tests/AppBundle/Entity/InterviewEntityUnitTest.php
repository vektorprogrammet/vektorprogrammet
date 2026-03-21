<?php

namespace Tests\App\Entity;

use App\Interview\Domain\ValueObjects\InterviewStatusType;
use App\Interview\Infrastructure\Entity\Interview;
use App\Interview\Infrastructure\Entity\InterviewAnswer;
use App\Interview\Infrastructure\Entity\InterviewSchema;
use App\Interview\Infrastructure\Entity\InterviewScore;
use App\Identity\Infrastructure\Entity\User;
use PHPUnit\Framework\TestCase;

class InterviewEntityUnitTest extends TestCase
{
    public function testSetInterviewSchema()
    {
        $interview = new Interview();
        $intSchema = new InterviewSchema();

        $interview->setInterviewSchema($intSchema);

        $this->assertEquals($intSchema, $interview->getInterviewSchema());
    }

    public function testSetInterviewer()
    {
        $interview = new Interview();
        $interviewer = new User();

        $interview->setInterviewer($interviewer);

        $this->assertEquals($interviewer, $interview->getInterviewer());
    }

    public function testAddInterviewAnswer()
    {
        $interview = new Interview();
        $answer = new InterviewAnswer();

        $interview->addInterviewAnswer($answer);

        $this->assertContains($answer, $interview->getInterviewAnswers());
    }

    public function testRemoveInterviewAnswer()
    {
        $interview = new Interview();
        $answer = new InterviewAnswer();

        $interview->addInterviewAnswer($answer);

        $this->assertContains($answer, $interview->getInterviewAnswers());

        $interview->removeInterviewAnswer($answer);

        $this->assertNotContains($answer, $interview->getInterviewAnswers());
    }

    public function testSetInterviewScore()
    {
        $interview = new Interview();
        $intScore = new InterviewScore();

        $interview->setInterviewScore($intScore);

        $this->assertEquals($intScore, $interview->getInterviewScore());
    }

    public function testSetInterviewed()
    {
        $interview = new Interview();

        $interview->setInterviewed(true);

        $this->assertTrue($interview->getInterviewed());
    }

    public function testSetScheduled()
    {
        $interview = new Interview();
        $date = new \DateTime();

        $interview->setScheduled($date);

        $this->assertEquals($date, $interview->getScheduled());
    }

    public function testSetConducted()
    {
        $interview = new Interview();
        $date = new \DateTime();

        $interview->setConducted($date);

        $this->assertEquals($date, $interview->getConducted());
    }

    public function testValidInterviewStatusTransition()
    {
        $interview = new Interview();
        // Constructor sets NO_CONTACT; valid transition is to PENDING
        $interview->setInterviewStatus(InterviewStatusType::PENDING);
        $this->assertEquals(InterviewStatusType::PENDING, $interview->getInterviewStatus());
    }

    public function testInvalidInterviewStatusTransitionThrows()
    {
        $interview = new Interview();
        // NO_CONTACT -> REQUEST_NEW_TIME is not a valid transition
        $this->expectException(\InvalidArgumentException::class);
        $interview->setInterviewStatus(InterviewStatusType::REQUEST_NEW_TIME);
    }

    public function testInterviewSelfTransitionAllowed()
    {
        $interview = new Interview();
        // Staying at NO_CONTACT should not throw
        $interview->setInterviewStatus(InterviewStatusType::NO_CONTACT);
        $this->assertEquals(InterviewStatusType::NO_CONTACT, $interview->getInterviewStatus());
    }

    public function testTerminalCancelledStatusRejectsTransition()
    {
        $interview = new Interview();
        $interview->setInterviewStatus(InterviewStatusType::PENDING);
        $interview->setInterviewStatus(InterviewStatusType::CANCELLED);
        // CANCELLED has no valid outbound transitions
        $this->expectException(\InvalidArgumentException::class);
        $interview->setInterviewStatus(InterviewStatusType::PENDING);
    }
}

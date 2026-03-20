<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Rules;

use App\Interview\Domain\Rules\InterviewCounter;
use App\Interview\Domain\ValueObjects\Suitability;
use App\Interview\Infrastructure\Entity\Interview;
use App\Interview\Infrastructure\Entity\InterviewScore;
use PHPUnit\Framework\TestCase;

class InterviewCounterTest extends TestCase
{
    private InterviewCounter $counter;

    protected function setUp(): void
    {
        $this->counter = new InterviewCounter();
    }

    public function testCountMatchingSuitability(): void
    {
        $score = $this->createMock(InterviewScore::class);
        $score->method('getSuitableAssistant')->willReturn('Ja');

        $interview = $this->createMock(Interview::class);
        $interview->method('getInterviewScore')->willReturn($score);

        $count = $this->counter->count([$interview], Suitability::Yes);

        $this->assertSame(1, $count);
    }

    public function testNullScoreIsSkipped(): void
    {
        $interview = $this->createMock(Interview::class);
        $interview->method('getInterviewScore')->willReturn(null);

        $count = $this->counter->count([$interview], Suitability::Yes);

        $this->assertSame(0, $count);
    }

    public function testEmptyArrayReturnsZero(): void
    {
        $count = $this->counter->count([], Suitability::Yes);

        $this->assertSame(0, $count);
    }

    public function testNonMatchingSuitabilityNotCounted(): void
    {
        $score = $this->createMock(InterviewScore::class);
        $score->method('getSuitableAssistant')->willReturn('Nei');

        $interview = $this->createMock(Interview::class);
        $interview->method('getInterviewScore')->willReturn($score);

        $count = $this->counter->count([$interview], Suitability::Yes);

        $this->assertSame(0, $count);
    }

    public function testMixedSuitabilitiesOnlyCountsMatches(): void
    {
        $scoreYes = $this->createMock(InterviewScore::class);
        $scoreYes->method('getSuitableAssistant')->willReturn('Ja');

        $scoreMaybe = $this->createMock(InterviewScore::class);
        $scoreMaybe->method('getSuitableAssistant')->willReturn('Kanskje');

        $scoreNo = $this->createMock(InterviewScore::class);
        $scoreNo->method('getSuitableAssistant')->willReturn('Nei');

        $interviewYes = $this->createMock(Interview::class);
        $interviewYes->method('getInterviewScore')->willReturn($scoreYes);

        $interviewMaybe = $this->createMock(Interview::class);
        $interviewMaybe->method('getInterviewScore')->willReturn($scoreMaybe);

        $interviewNoScore = $this->createMock(Interview::class);
        $interviewNoScore->method('getInterviewScore')->willReturn(null);

        $interviewNo = $this->createMock(Interview::class);
        $interviewNo->method('getInterviewScore')->willReturn($scoreNo);

        $count = $this->counter->count(
            [$interviewYes, $interviewMaybe, $interviewNoScore, $interviewNo],
            Suitability::Yes
        );

        $this->assertSame(1, $count);
    }
}

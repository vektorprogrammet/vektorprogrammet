<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Rules;

use App\Survey\Domain\Rules\SurveyDataTransformer;
use App\Survey\Infrastructure\Entity\Survey;
use PHPUnit\Framework\TestCase;

class SurveyDataTransformerTest extends TestCase
{
    private SurveyDataTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new SurveyDataTransformer();
    }

    public function testTeamSurveyReturnsTeam(): void
    {
        $survey = $this->createMock(Survey::class);
        $survey->method('getTargetAudience')->willReturn(Survey::$TEAM_SURVEY);

        $this->assertSame('Team', $this->transformer->getSurveyTargetAudienceString($survey));
    }

    public function testAssistantSurveyReturnsAssistent(): void
    {
        $survey = $this->createMock(Survey::class);
        $survey->method('getTargetAudience')->willReturn(Survey::$ASSISTANT_SURVEY);

        $this->assertSame('Assistent', $this->transformer->getSurveyTargetAudienceString($survey));
    }

    public function testSchoolSurveyReturnsSkole(): void
    {
        $survey = $this->createMock(Survey::class);
        $survey->method('getTargetAudience')->willReturn(Survey::$SCHOOL_SURVEY);

        $this->assertSame('Skole', $this->transformer->getSurveyTargetAudienceString($survey));
    }

    public function testUnknownAudienceReturnsAndre(): void
    {
        $survey = $this->createMock(Survey::class);
        $survey->method('getTargetAudience')->willReturn(999);

        $this->assertSame('Andre', $this->transformer->getSurveyTargetAudienceString($survey));
    }

    public function testFormatTeamNamesWithSingleName(): void
    {
        $result = $this->transformer->formatTeamNames(['Alpha']);

        $this->assertSame('Alpha', $result);
    }

    public function testFormatTeamNamesWithTwoNamesUsesOg(): void
    {
        $result = $this->transformer->formatTeamNames(['Alpha', 'Beta']);

        // Last comma replaced with ' og'
        $this->assertSame('Alpha og Beta', $result);
    }

    public function testFormatTeamNamesWithThreeNamesReplacesLastComma(): void
    {
        $result = $this->transformer->formatTeamNames(['Alpha', 'Beta', 'Gamma']);

        // Only the last comma is replaced
        $this->assertSame('Alpha, Beta og Gamma', $result);
    }

    public function testFormatTeamNamesWithFourNames(): void
    {
        $result = $this->transformer->formatTeamNames(['Alpha', 'Beta', 'Gamma', 'Delta']);

        $this->assertSame('Alpha, Beta, Gamma og Delta', $result);
    }
}

<?php

namespace Tests\App\Entity;

use App\Interview\Infrastructure\Entity\InterviewScore;
use PHPUnit\Framework\TestCase;
use App\Interview\Domain\ValueObjects\Suitability;

class InterviewScoreEntityUnitTest extends TestCase
{
    public function testSetExplanatoryPower()
    {
        $intScore = new InterviewScore();

        $intScore->setExplanatoryPower(3);

        $this->assertEquals(3, $intScore->getExplanatoryPower());
    }

    public function testSetRoleModel()
    {
        $intScore = new InterviewScore();

        $intScore->setRoleModel(3);

        $this->assertEquals(3, $intScore->getRoleModel());
    }

    public function testGetSum()
    {
        $intScore = new InterviewScore();

        $intScore->setExplanatoryPower(3);
        $intScore->setRoleModel(3);

        $this->assertEquals(6, $intScore->getSum());
    }

    public function testSetSuitableAssistantAcceptsValidValue()
    {
        $intScore = new InterviewScore();
        $intScore->setSuitableAssistant(Suitability::Yes->value);
        $this->assertEquals(Suitability::Yes->value, $intScore->getSuitableAssistant());
    }

    public function testSetSuitableAssistantRejectsInvalidValue()
    {
        $intScore = new InterviewScore();
        $this->expectException(\InvalidArgumentException::class);
        $intScore->setSuitableAssistant('invalid_value');
    }
}

<?php

namespace App\Tests\App\Interview\Infrastructure\Entity;

use App\Interview\Infrastructure\Entity\InterviewScore;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class InterviewScoreValidationTest extends TestCase
{
    private function makeScore(int $explanatoryPower, int $roleModel, int $suitability): InterviewScore
    {
        $score = new InterviewScore();
        $score->setExplanatoryPower($explanatoryPower);
        $score->setRoleModel($roleModel);
        $score->setSuitability($suitability);
        $score->setSuitableAssistant('Ja');
        return $score;
    }

    public function testScoreAboveMaxProducesViolation(): void
    {
        $validator = Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();
        $score = $this->makeScore(11, 5, 5);

        $violations = $validator->validate($score);

        $this->assertGreaterThan(0, count($violations));
        $paths = array_map(fn($v) => $v->getPropertyPath(), iterator_to_array($violations));
        $this->assertContains('explanatoryPower', $paths);
    }

    public function testScoreBelowMinProducesViolation(): void
    {
        $validator = Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();
        $score = $this->makeScore(5, -1, 5);

        $violations = $validator->validate($score);

        $this->assertGreaterThan(0, count($violations));
        $paths = array_map(fn($v) => $v->getPropertyPath(), iterator_to_array($violations));
        $this->assertContains('roleModel', $paths);
    }

    public function testValidScoreProducesNoViolation(): void
    {
        $validator = Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();
        $score = $this->makeScore(5, 7, 3);

        $violations = $validator->validate($score);

        $this->assertCount(0, $violations);
    }
}

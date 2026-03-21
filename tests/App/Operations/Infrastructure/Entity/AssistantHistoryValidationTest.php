<?php

namespace App\Tests\App\Operations\Infrastructure\Entity;

use App\Operations\Infrastructure\Entity\AssistantHistory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class AssistantHistoryValidationTest extends TestCase
{
    public function testInvalidWorkdaysProducesViolation(): void
    {
        $validator = Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();
        $ah = new AssistantHistory();
        $ah->setWorkdays('not-a-number');
        $ah->setDay('Mandag');
        $ah->setBolk('Bolk 1');

        $violations = $validator->validate($ah);
        $paths = array_map(fn($v) => $v->getPropertyPath(), iterator_to_array($violations));
        $this->assertContains('workdays', $paths, 'Expected violation on workdays for non-numeric value');
    }

    public function testValidWorkdaysProducesNoViolation(): void
    {
        $validator = Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();
        $ah = new AssistantHistory();
        $ah->setWorkdays('4');
        $ah->setDay('Mandag');
        $ah->setBolk('Bolk 1');

        $violations = $validator->validate($ah);
        $paths = array_map(fn($v) => $v->getPropertyPath(), iterator_to_array($violations));
        $this->assertNotContains('workdays', $paths, 'Valid workdays should produce no violation');
    }

    public function testInvalidBolkProducesViolation(): void
    {
        $validator = Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();
        $ah = new AssistantHistory();
        $ah->setWorkdays('4');
        $ah->setDay('Mandag');
        $ah->setBolk('invalid format');

        $violations = $validator->validate($ah);
        $paths = array_map(fn($v) => $v->getPropertyPath(), iterator_to_array($violations));
        $this->assertContains('bolk', $paths, 'Expected violation on bolk for invalid format');
    }

    public function testValidBolkFormatsProduceNoViolation(): void
    {
        $validator = Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();

        foreach (['Bolk 1', 'Bolk 2', 'Bolk 1, Bolk 2', 'Bolk 1, Bolk 2, Bolk 3'] as $bolk) {
            $ah = new AssistantHistory();
            $ah->setWorkdays('4');
            $ah->setDay('Mandag');
            $ah->setBolk($bolk);

            $violations = $validator->validate($ah);
            $paths = array_map(fn($v) => $v->getPropertyPath(), iterator_to_array($violations));
            $this->assertNotContains('bolk', $paths, "Valid bolk '$bolk' should produce no violation");
        }
    }

    public function testInvalidDayProducesViolation(): void
    {
        $validator = Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();
        $ah = new AssistantHistory();
        $ah->setWorkdays('4');
        $ah->setDay('Monday');
        $ah->setBolk('Bolk 1');

        $violations = $validator->validate($ah);
        $paths = array_map(fn($v) => $v->getPropertyPath(), iterator_to_array($violations));
        $this->assertContains('day', $paths, 'Expected violation on day for non-Norwegian day name');
    }

    public function testValidNorwegianDaysProduceNoViolation(): void
    {
        $validator = Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();

        foreach (['Mandag', 'Tirsdag', 'Onsdag', 'Torsdag', 'Fredag'] as $day) {
            $ah = new AssistantHistory();
            $ah->setWorkdays('4');
            $ah->setDay($day);
            $ah->setBolk('Bolk 1');

            $violations = $validator->validate($ah);
            $paths = array_map(fn($v) => $v->getPropertyPath(), iterator_to_array($violations));
            $this->assertNotContains('day', $paths, "Valid day '$day' should produce no violation");
        }
    }
}

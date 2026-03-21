<?php

namespace App\Tests\App\Scheduling\Infrastructure\Entity;

use App\Scheduling\Infrastructure\Entity\SchoolCapacity;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class SchoolCapacityValidationTest extends TestCase
{
    public function testNegativeValueProducesViolation(): void
    {
        $validator = Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();

        $capacity = new SchoolCapacity();
        $capacity->setMonday(-1);

        $violations = $validator->validate($capacity);

        $paths = array_map(fn($v) => $v->getPropertyPath(), iterator_to_array($violations));
        $this->assertContains('monday', $paths, 'Expected a violation on monday when value is -1');
    }

    public function testZeroValueProducesNoViolation(): void
    {
        $validator = Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();

        $capacity = new SchoolCapacity();
        // Constructor defaults all to 0
        $violations = $validator->validate($capacity);

        $paths = array_map(fn($v) => $v->getPropertyPath(), iterator_to_array($violations));
        foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday'] as $day) {
            $this->assertNotContains($day, $paths, "Expected no violation on $day when value is 0");
        }
    }

    public function testNegativeValueOnEachDayProducesViolation(): void
    {
        $validator = Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();

        foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday'] as $day) {
            $capacity = new SchoolCapacity();
            $setter = 'set'.ucfirst($day);
            $capacity->$setter(-5);

            $violations = $validator->validate($capacity);
            $paths = array_map(fn($v) => $v->getPropertyPath(), iterator_to_array($violations));
            $this->assertContains($day, $paths, "Expected a violation on $day when value is -5");
        }
    }
}

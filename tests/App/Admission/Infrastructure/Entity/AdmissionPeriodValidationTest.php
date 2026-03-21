<?php

namespace App\Tests\App\Admission\Infrastructure\Entity;

use App\Admission\Infrastructure\Entity\AdmissionPeriod;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class AdmissionPeriodValidationTest extends TestCase
{
    public function testStartDateAfterEndDateProducesViolation(): void
    {
        $validator = Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();

        $period = new AdmissionPeriod();
        $period->setStartDate(new \DateTime('2024-12-01'));
        $period->setEndDate(new \DateTime('2024-11-01')); // end before start

        $violations = $validator->validate($period);

        $paths = array_map(fn($v) => $v->getPropertyPath(), iterator_to_array($violations));
        $this->assertContains('startDate', $paths, 'Expected a violation on startDate when startDate >= endDate');
    }

    public function testStartDateEqualToEndDateProducesViolation(): void
    {
        $validator = Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();

        $period = new AdmissionPeriod();
        $period->setStartDate(new \DateTime('2024-11-01'));
        $period->setEndDate(new \DateTime('2024-11-01')); // equal

        $violations = $validator->validate($period);

        $paths = array_map(fn($v) => $v->getPropertyPath(), iterator_to_array($violations));
        $this->assertContains('startDate', $paths, 'Expected a violation on startDate when startDate == endDate');
    }

    public function testStartDateBeforeEndDateProducesNoViolation(): void
    {
        $validator = Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();

        $period = new AdmissionPeriod();
        $period->setStartDate(new \DateTime('2024-10-01'));
        $period->setEndDate(new \DateTime('2024-12-01')); // valid

        $violations = $validator->validate($period);

        $paths = array_map(fn($v) => $v->getPropertyPath(), iterator_to_array($violations));
        $this->assertNotContains('startDate', $paths, 'Expected no violation on startDate when startDate < endDate');
    }
}

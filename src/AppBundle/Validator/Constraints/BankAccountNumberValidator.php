<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class BankAccountNumberValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        dump('validator runs'); // Kjører ikke :(
        // An implementation of the MOD11 validation algorithm used for norwegian bank account numbers
        $valid = true;

        $weightNumbers = array(2, 3, 4, 5, 6, 7, 2, 3, 4, 5);
        $numbers = str_split($value);

        // Invalid length
        if ((sizeof($numbers) - 1) != sizeof($weightNumbers)) {
            $valid = false;
        }

        // Take cross product of the numbers and weight numbers (except final control digit in $numbers)
        $cross = 0;
        for ($i = 0; $i < sizeof($weightNumbers); ++$i) {
            $cross += $numbers[$i] * $weightNumbers[$i];
        }

        // Invalid MOD11
        if ($cross % 11 != $numbers[sizeof($numbers) - 1]) {
            $valid = false;
        }

        if (!$valid) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}

<?php


namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class VektorEmailValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value) {
            return;
        }

        $emailEnding = '@vektorprogrammet.no';
        $emailHasCorrectEnding = substr($value, strlen($value) - strlen($emailEnding)) === $emailEnding;

        if (!$emailHasCorrectEnding) {
            $this->context->buildViolation($constraint->message)
                          ->addViolation();
        }
    }
}

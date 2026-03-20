<?php

namespace App\Admission\Infrastructure\Validator;

use App\Admission\Infrastructure\Entity\InfoMeeting;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class InfoMeetingValidator extends ConstraintValidator
{
    /**
     * Checks if the info meeting is valid.
     *
     * @param mixed      $infoMeeting The info meeting that should be validated
     * @param Constraint $constraint  The constraint for the validation
     */
    public function validate(mixed $infoMeeting, Constraint $constraint): void
    {
        if (!$infoMeeting instanceof InfoMeeting) {
            return;
        }

        assert($constraint instanceof \App\Admission\Infrastructure\Validator\InfoMeeting);

        if ($infoMeeting->isShowOnPage() && $infoMeeting->getDate() === null) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}

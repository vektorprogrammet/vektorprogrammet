<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use App\Entity\InfoMeeting;

class InfoMeetingValidator extends ConstraintValidator
{

    /**
     * Checks if the info meeting is valid.
     *
     * @param InfoMeeting $infoMeeting The info meeting that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($infoMeeting, Constraint $constraint)
    {
        if (!$infoMeeting) {
            return;
        }

        if ($infoMeeting->isShowOnPage() && $infoMeeting->getDate() === null) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}

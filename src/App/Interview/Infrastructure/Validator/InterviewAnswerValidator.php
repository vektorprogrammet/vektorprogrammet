<?php

namespace App\Interview\Infrastructure\Validator;

use App\Interview\Infrastructure\Entity\InterviewAnswer as InterviewAnswerEntity;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class InterviewAnswerValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed      $value      The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        \assert($constraint instanceof \App\Interview\Infrastructure\Validator\InterviewAnswer);

        $interviewAnswer = $this->context->getObject();
        if (!$interviewAnswer instanceof InterviewAnswerEntity) {
            return;
        }

        $questionType = $interviewAnswer->getInterviewQuestion()->getType();
        if ($questionType === 'check') {
            return;
        }
        if ($value === null || $value === '' || $value === []) {
            $this->context->buildViolation($constraint->message)
                          ->addViolation();
        }
    }
}

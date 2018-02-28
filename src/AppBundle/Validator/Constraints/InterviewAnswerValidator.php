<?php


namespace AppBundle\Validator\Constraints;

use AppBundle\Entity\InterviewAnswer;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class InterviewAnswerValidator extends ConstraintValidator
{

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        /**
         * @var InterviewAnswer $interviewAnswer
         */
        $interviewAnswer = $this->context->getObject();
        if (!$interviewAnswer instanceof InterviewAnswer) {
            return;
        }

        $questionType = $interviewAnswer->getInterviewQuestion()->getType();
        if ($questionType === 'check') {
            return;
        }
        if (empty($value)) {
            $this->context->buildViolation($constraint->message)
                          ->addViolation();
        }
    }
}

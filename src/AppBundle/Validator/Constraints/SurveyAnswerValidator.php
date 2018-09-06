<?php
/**
 * Created by IntelliJ IDEA.
 * User: Amir Ahmed
 * Date: 06.09.2018
 * Time: 18:18
 */

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraints as Assert;

class SurveyAnswerValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if ($value->getSurveyQuestion()->getOptional() || $value->getType()==='check') {
            return;
        }


        $value->addPropertyConstraint('answer', new Assert\NotBlank());
    }
}

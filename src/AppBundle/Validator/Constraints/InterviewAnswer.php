<?php


namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class InterviewAnswer extends Constraint
{
    public $message = 'Dette feltet kan ikke være tomt';
}

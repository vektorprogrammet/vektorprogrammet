<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class InterviewAnswer extends Constraint
{
    public $message = 'Dette feltet kan ikke være tomt';
}

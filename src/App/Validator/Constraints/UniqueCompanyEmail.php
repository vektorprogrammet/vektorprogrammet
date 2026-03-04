<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class UniqueCompanyEmail extends Constraint
{
    public $message = 'E-posten "{{ email }}" er allerede i bruk';
}

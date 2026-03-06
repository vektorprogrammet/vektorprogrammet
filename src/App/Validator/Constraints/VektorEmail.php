<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class VektorEmail extends Constraint
{
    public $message = 'E-postadressen må slutte med "@vektorprogrammet.no"';
}

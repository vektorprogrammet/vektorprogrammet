<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
class ApplicationEmail extends Constraint
{
    public $message = 'En søknad med {{ email }} har allerede blitt registert';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}

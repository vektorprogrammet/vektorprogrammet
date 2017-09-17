<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueCompanyEmail extends Constraint
{
    public $message = 'E-posten "{{ email }}" er allerede i bruk';
}

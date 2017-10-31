<?php


namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class VektorEmail extends Constraint
{
    public $message = 'E-postadressen må slutte med "@vektorprogrammet.no"';
}

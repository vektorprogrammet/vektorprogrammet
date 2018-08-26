<?php


namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ApplicationEmail extends Constraint
{
    public $message = 'En søknad med {{ email }} har allerede blitt registert';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}

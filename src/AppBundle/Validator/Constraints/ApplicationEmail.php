<?php


namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ApplicationEmail extends Constraint
{
    public $message = 'En søknad med {{ email }} har allerede blitt registert';

    public function getTargets(): string|array
    {
        return self::CLASS_CONSTRAINT;
    }
}

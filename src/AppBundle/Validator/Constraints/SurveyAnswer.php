<?php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class SurveyAnswer extends Constraint
{
    public $message = 'Felt ikke gyldig';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}

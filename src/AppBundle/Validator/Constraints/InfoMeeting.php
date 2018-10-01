<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class InfoMeeting extends Constraint
{
    public $message = "Infomøtet må ha en dato for å kunne vises på nettsiden";

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}

<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueTeamNameInDepartment extends Constraint
{
    public $message = 'Teamet "{{ teamName }}" finnes allerede i avdelingen "{{ departmentCity }}".';
}

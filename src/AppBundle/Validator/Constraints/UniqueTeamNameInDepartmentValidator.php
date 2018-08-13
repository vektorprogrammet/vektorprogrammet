<?php

namespace AppBundle\Validator\Constraints;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueTeamNameInDepartmentValidator extends ConstraintValidator
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function validate($value, Constraint $constraint)
    {
        $team = $this->context->getObject();
        $department = $team->getDepartment();
        if ($department === null) {
            return; // Impossible to check if valid when department is null
        }
        $departmentCity = $team->getDepartment()->getCity();
        // Check if a team exists in $department with the same name
        $teamsWithSameName = $this->em->getRepository('AppBundle:Team')
            ->findByDepartmentAndName($departmentCity, $value);
        if (!empty($teamsWithSameName)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ departmentCity }}', $departmentCity)
                ->setParameter('{{ teamName }}', $value)
                ->addViolation();
        }
    }
}

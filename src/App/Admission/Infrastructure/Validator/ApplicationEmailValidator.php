<?php

namespace App\Admission\Infrastructure\Validator;

use App\Admission\Infrastructure\Entity\Application;
use App\Admission\Infrastructure\ApplicationAdmission;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ApplicationEmailValidator extends ConstraintValidator
{
    public function __construct(private readonly ApplicationAdmission $admissionManager)
    {
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed      $application The value that should be validated
     * @param Constraint $constraint  The constraint for the validation
     */
    public function validate(mixed $application, Constraint $constraint): void
    {
        if (!$application instanceof Application) {
            return;
        }

        assert($constraint instanceof \App\Admission\Infrastructure\Validator\ApplicationEmail);

        $user = $application->getUser();
        $hasAlreadyApplied = $this->admissionManager->userHasAlreadyApplied($user);

        if ($hasAlreadyApplied) {
            $this->context->buildViolation($constraint->message)
                          ->setParameter('{{ email }}', $user->getEmail())
                          ->addViolation();
        }
    }
}

<?php


namespace AppBundle\Validator\Constraints;

use AppBundle\Entity\Application;
use AppBundle\Service\ApplicationAdmission;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ApplicationEmailValidator extends ConstraintValidator
{
    private $admissionManager;

    public function __construct(ApplicationAdmission $admissionManager)
    {
        $this->admissionManager = $admissionManager;
    }


    /**
     * Checks if the passed value is valid.
     *
     * @param Application $application The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($application, Constraint $constraint)
    {
        if (!$application) {
            return;
        }

        $user = $application->getUser();
        $hasAlreadyApplied = $this->admissionManager->userHasAlreadyApplied($user);

        if ($hasAlreadyApplied) {
            $this->context->buildViolation($constraint->message)
                          ->setParameter('{{ email }}', $user->getEmail())
                          ->addViolation();
        }
    }
}

<?php


namespace AppBundle\Validator\Constraints;

use AppBundle\Entity\Team;
use AppBundle\Entity\User;
use AppBundle\Google\GoogleAPI;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueCompanyEmailValidator extends ConstraintValidator
{
    private $em;
    private $googleAPI;

    public function __construct(EntityManagerInterface $em, GoogleAPI $googleAPI)
    {
        $this->em = $em;
        $this->googleAPI = $googleAPI;
    }


    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value || !$this->objectHasChanged($value)) {
            return;
        }

        $googleEmails = $this->googleAPI->getAllEmailsInUse();
        $teamEmails = $this->em->getRepository('AppBundle:Team')->findAllEmails();
        $userCompanyEmails = $this->em->getRepository('AppBundle:User')->findAllCompanyEmails();
        $allEmails = array_merge($googleEmails, $teamEmails, $userCompanyEmails);

        if (array_search($value, $allEmails) !== false) {
            $this->context->buildViolation($constraint->message)
                          ->setParameter('{{ email }}', $value)
                          ->addViolation();
        }
    }

    private function objectHasChanged($value)
    {
        $object = $this->context->getObject();
        $oldObject = $this->em
            ->getUnitOfWork()
            ->getOriginalEntityData($object);

        if ($object instanceof User && key_exists('companyEmail', $oldObject) && $oldObject['companyEmail'] === $value) {
            return false;
        } elseif ($object instanceof Team && key_exists('email', $oldObject) && $oldObject['email'] === $value) {
            return false;
        }

        return true;
    }
}

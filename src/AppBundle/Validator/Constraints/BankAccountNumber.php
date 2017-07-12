<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class BankAccountNumber extends Constraint
{
    public $message = 'Ugyldig kontonummer. Kontonummer skal skrives uten mellomrom eller punktum.';

    public function validatedBy()
    {
        return BankAccountNumberValidator::class;
    }
}

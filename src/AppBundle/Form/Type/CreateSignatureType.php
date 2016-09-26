<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CreateSignatureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', 'text', array(
                'label' => 'Beskrivelse (Feks. Leder, Vektorprogrammet)',
            ))
            ->add('signature_path', 'file', array(
                'required' => false,
                'data_class' => null,
                'label' => 'Signaturbilde',
            ));
    }
}

<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SubstituteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $workChoices = array(
            'Bra' => 'Bra',
            'Ok' => 'Ok',
            'Ikke' => 'Ikke',
        );

        $builder
            ->add('firstName', 'text', array('label' => 'Fornavn'))
            ->add('lastName', 'text', array('label' => 'Etternavn'))
            ->add('phone', 'text', array('label' => 'Tlf'))
            ->add('email', 'text', array('label' => 'E-post'));

        $builder->add('monday', 'choice', array(
            'label' => 'Mandag',
            'choices' => $workChoices,
            'expanded' => true,
        ));

        $builder->add('tuesday', 'choice', array(
            'label' => 'Tirsdag',
            'choices' => $workChoices,
            'expanded' => true,
        ));

        $builder->add('wednesday', 'choice', array(
            'label' => 'Onsdag',
            'choices' => $workChoices,
            'expanded' => true,
        ));

        $builder->add('thursday', 'choice', array(
            'label' => 'Torsdag',
            'choices' => $workChoices,
            'expanded' => true,
        ));

        $builder->add('friday', 'choice', array(
            'label' => 'Fredag',
            'choices' => $workChoices,
            'expanded' => true,
        ));

        $builder->add('save', 'submit', array(
            'label' => 'Lagre',
        ));
    }

    public function getName()
    {
        return 'substitute';
    }
}

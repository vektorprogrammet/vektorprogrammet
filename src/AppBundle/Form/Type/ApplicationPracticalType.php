<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ApplicationPracticalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $workChoices = array(
            "Bra" => "Bra",
            "Ikke" => "Ikke"
        );

        $builder->add('doublePosition', 'choice', array(
            'label' => 'Kunne du tenke deg dobbelt stilling? Altså en gang i uka i 8 uker?',
            'help' => "Info om når bolkene er. Det teller ikke negativt dersom man ikke ønsker det, viktig at intervjuobjektet skjønner dette.",
            'choices' => array(
                0 => "Nei",
                1 => "Ja",
            ),
            'expanded' => true,
            'multiple' => false
        ));

        $builder->add('preferredGroup', 'choice', array(
            'label' => 'Har du et ønske om bolk?',
            'choices' => array(
                null => "Ingen",
                "Bolk 1" => "Bolk 1",
                "Bolk 2" => "Bolk 2",
            ),
            'expanded' => true,
            'multiple' => false
        ));

        $builder->add('monday', 'choice', array(
            'label' => 'Mandag',
            'choices' => $workChoices,
            'expanded' => true
        ));

        $builder->add('tuesday', 'choice', array(
            'label' => 'Tirsdag',
            'choices' => $workChoices,
            'expanded' => true
        ));

        $builder->add('wednesday', 'choice', array(
            'label' => 'Onsdag',
            'choices' => $workChoices,
            'expanded' => true
        ));

        $builder->add('thursday', 'choice', array(
            'label' => 'Torsdag',
            'choices' => $workChoices,
            'expanded' => true
        ));

        $builder->add('friday', 'choice', array(
            'label' => 'Fredag',
            'choices' => $workChoices,
            'expanded' => true
        ));

        $builder->add('english', 'choice', array(
            'label' => 'Vi har en internasjonal skole. Har du lyst til å undervise på engelsk?',
            'help' => 'Det har ikke noe å si om du svarer ja eller nei på dette for om du blir tatt opp eller ikke.',
            'choices' => array(
                0 => "Nei",
                1 => "Ja"
            ),
            'expanded' => true,
            'multiple' => false
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Application',
            'inherit_data' => true,
        ));
    }

    public function getName()
    {
        return 'application';
    }
}
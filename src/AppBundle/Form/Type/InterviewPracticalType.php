<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InterviewPracticalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $workChoices = array(
            'Bra' => 'Bra',
            'Ikke' => 'Ikke',
        );

        $builder->add('doublePosition', 'choice', array(
            'label' => 'Kunne du tenke deg dobbelt stilling? Altså en gang i uka i 8 uker?',
            'help' => 'Info om når bolkene er. Det teller ikke negativt dersom man ikke ønsker det, viktig at intervjuobjektet skjønner dette.',
            'choices' => array(
                0 => 'Nei',
                1 => 'Ja',
            ),
            'expanded' => true,
            'multiple' => false,
        ));

        $builder->add('preferredGroup', 'choice', array(
            'label' => 'Har du et ønske om bolk?',
            'choices' => array(
                null => 'Ingen',
                'Bolk 1' => 'Bolk 1',
                'Bolk 2' => 'Bolk 2',
            ),
            'expanded' => true,
            'multiple' => false,
        ));

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

        $builder->add('language', 'choice', array(
            'label' => 'Vi har en internasjonal skole. Hvilke(t) språk ønsker du å undervise på?',
            'help' => 'Det er ingen spesiell fordel å velge det ene fremfor det andre.',
            'choices' => array(
                'Norsk' => 'Norsk',
                'Engelsk' => 'Engelsk',
                'Norsk og engelsk' => 'Norsk og engelsk',
            ),
            'expanded' => true,
            'multiple' => false,
        ));

        $builder->add('heardAboutFrom', 'choice', array(
            'label' => 'Hvor hørte du om Vektorprogrammet?',
            'choices' => array(
                'Blesting' => 'Blesting',
                'Stand' => 'Stand',
                'Infomail/nettsida/facebook etc' => 'Infomail/nettsida/facebook etc',
                'Bekjente' => 'Bekjente',
                'Bekjente i styret' => 'Bekjente i styret',
                'Plakater/flyers' => 'Plakater/Flyers',
                'Linjeforeningen (f.eks fadderukene)' => 'Linjeforeningen (f.eks fadderukene)',
            ),
            'expanded' => true,
            'multiple' => true,
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\InterviewPractical',
        ));
    }

    public function getName()
    {
        return 'interviewPractical';
    }
}

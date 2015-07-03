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
            "Bra" => "Bra",
            "Ok" => "Ok",
            "Ikke" => "Ikke"
        );

        $builder->add('position', 'choice', array(
            'label' => 'Kunne du tenke deg dobbelt stilling? Altså en gang i uka i 8 uker, eller 2 ganger i uka i 4 uker. Har du et ønske om bolk?',
            'help' => "Info om når bolkene er. Det teller ikke negativt dersom man ikke ønsker det, viktig at intervjuobjektet skjønner dette.",
            'choices' => array(
                "2x4" => "2 x 4",
                "1x8" => "1 x 8",
                "Nei" => "Nei",
                "Bolk 1" => "Bolk 1",
                "Bolk 2" => "Bolk 2"
            ),
            'expanded' => true,
            'multiple' => true
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

        $builder->add('substitute', 'choice', array(
            'label' => 'Dersom du ikke får stillingen som vektorassistent, vil du da være vikar for andre som melder frafall?',
            'choices' => array(
                1 => "Ja",
                0 => "Nei"
            ),
        ));

        $builder->add('english', 'choice', array(
            'label' => 'Er du komfortabel med engelsk?',
            'help' => 'Vi samarberider med den internasjonale skolen og hvis vi ikke for nok kvalifiserte utvekslingstudenter kunne du tenke deg å være på den internasjonale skolen? Det er helt lov å si nei',
            'choices' => array(
                0 => "Nei",
                1 => "Ja"
            ),
        ));

        $builder->add('heardAboutFrom', 'choice', array(
            'label' => 'Hvor hørte du om Vektorprogrammet?',
            'choices' => array(
                'Blesting' => 'Blesting',
                'Stand' => 'Stand',
                'Infomail/nettsida/facebook etc' => 'Infomail/nettsida/facebook etc',
                'Bekjente' => 'Bekjente',
                'Bekjente' => 'Bekjente i styret',
                'Plakater/flyers' => 'Plakater/Flyers',
                'Linjeforeningen (f.eks fadderukene)' => 'Linjeforeningen (f.eks fadderukene)'
            ),
            'expanded' => true,
            'multiple' => true
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
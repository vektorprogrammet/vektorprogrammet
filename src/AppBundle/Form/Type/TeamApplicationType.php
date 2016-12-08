<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamApplicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'Navn',
            ))
            ->add('email', 'email', array(
                'label' => 'Email',
            ))
            ->add('yearOfStudy', 'choice', array(
                'label' => 'Studieår',
                'choices' => array(
                    '1. klasse' => '1. klasse',
                    '2. klasse' => '2. klasse',
                    '3. klasse' => '3. klasse',
                    '4. klasse' => '4. klasse',
                    '5. klasse' => '5. klasse',
                ),
            ))
            ->add('fieldOfStudy', 'text', array(
                'label' => 'Linje',
            ))
            ->add('motivationText', 'textarea', array(
                'label' => 'Skriv kort om din motivasjon for vervet',
                'attr' => array('rows' => 4),
            ))
            ->add('biography', 'textarea', array(
                'label' => 'Skriv litt om deg selv',
                'attr' => array('rows' => 10),
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\TeamApplication',
        ));
    }

    public function getName()
    {
        return 'app_bundle_team_application_type';
    }
}

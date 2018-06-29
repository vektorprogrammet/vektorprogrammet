<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamApplicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'label' => 'Navn',
            ))
            ->add('email', EmailType::class, array(
                'label' => 'Email',
            ))
            ->add('phone', TelType::class, array(
                'label' => 'Telefon',
            ))
            ->add('yearOfStudy', ChoiceType::class, array(
                'label' => 'Årstrinn',
                'choices' => array(
                    '1. klasse' => '1. klasse',
                    '2. klasse' => '2. klasse',
                    '3. klasse' => '3. klasse',
                    '4. klasse' => '4. klasse',
                    '5. klasse' => '5. klasse',
                ),
            ))
            ->add('fieldOfStudy', TextType::class, array(
                'label' => 'Linje',
            ))
            ->add('motivationText', TextareaType::class, array(
                'label' => 'Skriv kort om din motivasjon for vervet',
                'attr' => array('rows' => 4),
            ))
            ->add('biography', TextareaType::class, array(
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

    public function getBlockPrefix()
    {
        return 'app_bundle_team_application_type';
    }
}

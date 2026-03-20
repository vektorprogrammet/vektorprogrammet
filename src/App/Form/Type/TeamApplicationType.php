<?php

namespace App\Form\Type;

use App\Support\Form\TelType;
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
            ->add('name', TextType::class, [
                'label' => 'Navn',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
            ->add('phone', TelType::class, [
                'label' => 'Telefon',
            ])
            ->add('yearOfStudy', ChoiceType::class, [
                'label' => 'Årstrinn',
                'choices' => [
                    '1. klasse' => '1. klasse',
                    '2. klasse' => '2. klasse',
                    '3. klasse' => '3. klasse',
                    '4. klasse' => '4. klasse',
                    '5. klasse' => '5. klasse',
                ],
            ])
            ->add('fieldOfStudy', TextType::class, [
                'label' => 'Linje',
            ])
            ->add('motivationText', TextareaType::class, [
                'label' => 'Skriv kort om din motivasjon for vervet',
                'attr' => ['rows' => 4],
            ])
            ->add('biography', TextareaType::class, [
                'label' => 'Skriv litt om deg selv',
                'attr' => ['rows' => 10],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => \App\Entity\TeamApplication::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_team_application_type';
    }
}

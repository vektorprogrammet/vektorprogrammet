<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateSchoolType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Navn',
            ])
            ->add('contactPerson', TextType::class, [
                'label' => 'Kontaktperson',
            ])
            ->add('phone', TextType::class, [
                'label' => 'Telefon',
            ])
            ->add('email', TextType::class, [
                'label' => 'E-post',
            ])
            ->add('international', ChoiceType::class, [
                'label' => 'Skolen er internasjonal',
                'choices' => [
                    'Ja' => 1,
                    'Nei' => 0,
                ],
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('active', ChoiceType::class, [
                'label' => 'Skolen er aktiv',
                'choices' => [
                    'Ja' => true,
                    'Nei' => false,
                ],
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Opprett',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => \App\Entity\School::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'createSchool';
    }
}

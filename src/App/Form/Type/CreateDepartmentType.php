<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateDepartmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Navn',
            ])
            ->add('shortName', TextType::class, [
                'label' => 'Forkortet navn',
            ])
            ->add('email', TextType::class, [
                'label' => 'E-post',
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse:',
                'required' => false,
            ])
            ->add('city', TextType::class, [
                'label' => 'By',
            ])
            ->add('latitude', TextType::class, [
                'label' => 'Latitude',
                'required' => false,
            ])
            ->add('longitude', TextType::class, [
                'label' => 'Longitude',
                'required' => false,
            ])
            ->add('slackChannel', TextType::class, [
                'label' => 'Privat Slack Channel',
                'required' => false,
                'attr' => ['placeholder' => 'eks. #styret_REGION'],
            ])
            ->add('active', CheckboxType::class, [
                'label' => 'Aktiv?',
                'required' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Opprett',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => \App\Entity\Department::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'createDepartment';
    }
}

<?php

namespace AppBundle\Form\Type;

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
            ->add('name', TextType::class, array(
                'label' => 'Navn',
            ))
            ->add('shortName', TextType::class, array(
                'label' => 'Forkortet navn',
            ))
            ->add('email', TextType::class, array(
                'label' => 'E-post',
            ))
            ->add('address', TextType::class, array(
                'label' => 'Adresse:',
                'required' => false
            ))
            ->add('city', TextType::class, array(
                'label' => 'By',
            ))
            ->add('latitude', TextType::class, array(
                'label' => 'Latitude',
                'required' => false
            ))
            ->add('longitude', TextType::class, array(
                'label' => 'Longitude',
                'required' => false
            ))
            ->add('slackChannel', TextType::class, array(
                'label' => 'Privat Slack Channel',
                'required' => false,
                'attr' => ['placeholder' => 'eks. #styret_REGION']
            ))
            ->add('active', CheckboxType::class, array(
                'label' => 'Aktiv?',
                'required' => false
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Opprett',
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Department',
        ));
    }

    public function getBlockPrefix()
    {
        return 'createDepartment';
    }
}

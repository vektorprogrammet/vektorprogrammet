<?php

namespace AppBundle\Form\Type;

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
            ->add('name', TextType::class, array(
                'label' => 'Navn',
            ))
            ->add('contactPerson', TextType::class, array(
                'label' => 'Kontaktperson',
            ))
            ->add('phone', TextType::class, array(
                'label' => 'Telefon',
            ))
            ->add('email', TextType::class, array(
                'label' => 'E-post',
            ))
            ->add('international', ChoiceType::class, array(
                'label' => 'Skolen er internasjonal',
                'choices' => array(
                    'Ja' => 1,
                    'Nei' => 0,
                ),
                'expanded' => true,
                'multiple' => false,
            ))
            ->add('active', ChoiceType::class, array(
                'label' => 'Skolen er aktiv',
                'choices' => array(
                    'Ja' => true,
                    'Nei' => false,
                ),
                'expanded' => true,
                'multiple' => false,
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Opprett',
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\School',
        ));
    }

    public function getBlockPrefix()
    {
        return 'createSchool';
    }
}

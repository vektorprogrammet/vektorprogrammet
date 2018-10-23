<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FieldOfStudyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'label' => 'Fullt navn',
                'attr' => array(
                    'placeholder' => 'Eks: Datateknikk',
                ),
            ))
            ->add('shortName', TextType::class, array(
                'label' => 'Forkortelse',
                'attr' => array(
                    'placeholder' => 'Eks: MTDT',
                ),
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\FieldOfStudy',
        ));
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_field_of_study_type';
    }
}

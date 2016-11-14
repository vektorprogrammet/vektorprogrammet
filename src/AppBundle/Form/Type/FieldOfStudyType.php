<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FieldOfStudyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'Fullt navn',
                'attr' => array(
                    'placeholder' => 'Eks: Datateknikk',
                ),
            ))
            ->add('short_name', 'text', array(
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

    public function getName()
    {
        return 'app_bundle_field_of_study_type';
    }
}

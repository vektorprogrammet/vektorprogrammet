<?php

namespace AppBundle\Form;

use AppBundle\Entity\Interview;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InterviewNewTimeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('newTimeMessage', TextareaType::class, array (
                'label' => 'Melding',
                'attr' => array('rows' => '5'),
            ))
            ->add('send', SubmitType::class, array(
                'label' => 'Bekreft',
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Interview::class
        ));
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_interview_new_time_type';
    }
}

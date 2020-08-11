<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditAdmissionPeriodType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate', DateTimeType::class, array(
                'label' => 'Opptak starttidspunkt',
                'format' => 'dd.MM.yyyy HH:mm',
                'widget' => 'single_text',
                'attr' => array(
                    'placeholder' => 'Klikk for å velge tidspunkt',
                ),
            ))
            ->add('endDate', DateTimeType::class, array(
                'label' => 'Opptak sluttidspunkt',
                'format' => 'dd.MM.yyyy HH:mm',
                'widget' => 'single_text',
                'attr' => array(
                    'placeholder' => 'Klikk for å velge tidspunkt',
                ),
            ))
            ->add('infoMeeting', InfoMeetingType::class, [
                'label' => 'Infomøte',
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\AdmissionPeriod',
        ));
    }

    public function getBlockPrefix()
    {
        return 'editAdmissionPeriod';
    }
}

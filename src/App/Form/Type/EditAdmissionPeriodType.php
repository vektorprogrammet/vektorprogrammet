<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditAdmissionPeriodType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate', DateTimeType::class, [
                'label' => 'Opptak starttidspunkt',
                'format' => 'dd.MM.yyyy HH:mm',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'placeholder' => 'Klikk for å velge tidspunkt',
                ],
            ])
            ->add('endDate', DateTimeType::class, [
                'label' => 'Opptak sluttidspunkt',
                'format' => 'dd.MM.yyyy HH:mm',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'placeholder' => 'Klikk for å velge tidspunkt',
                ],
            ])
            ->add('infoMeeting', InfoMeetingType::class, [
                'label' => 'Infomøte',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => \App\Entity\AdmissionPeriod::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'editAdmissionPeriod';
    }
}

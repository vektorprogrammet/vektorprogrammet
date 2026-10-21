<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditAdmissionPeriodType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate', DateTimeType::class, array(
                'html5' => false,
                'label' => 'Opptak starttidspunkt',
                'format' => 'dd.MM.yyyy HH:mm',
                'widget' => 'single_text',
                'attr' => array(
                    'placeholder' => 'Klikk for å velge tidspunkt',
                ),
            ))
            ->add('endDate', DateTimeType::class, array(
                'html5' => false,
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\AdmissionPeriod',
        ));
    }

    public function getBlockPrefix(): string
    {
        return 'editAdmissionPeriod';
    }
}

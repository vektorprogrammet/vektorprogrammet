<?php

namespace App\Interview\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ScheduleInterviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('from', EmailType::class, [
                'label' => 'Avsender',
            ])
            ->add('to', EmailType::class, [
                'label' => 'Mottaker',
            ])

            ->add('datetime', DateTimeType::class, [
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy HH:mm',
                'html5' => false,
                'label' => 'Tidspunkt',
                'attr' => ['placeholder' => 'Klikk for å velge tidspunkt'],
            ])

            ->add('room', TextType::class, [
                'label' => 'Rom',
            ])
            ->add('mapLink', TextType::class, [
                'label' => false,
                'required' => false,
            ])
            ->add('campus', TextType::class, [
                'label' => 'Campus',
                'required' => false,
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Melding',
                'attr' => ['rows' => '5'],
            ])
            ->add('saveAndSend', SubmitType::class, [
                'label' => 'Send invitasjon på sms og e-post',
            ])
            ->add('preview', SubmitType::class, [
                'label' => 'Forhåndsvis',
            ])
        ;
    }

    public function getBlockPrefix()
    {
        return 'scheduleInterview';
    }
}

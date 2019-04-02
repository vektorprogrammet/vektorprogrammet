<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
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
            ->add('from', EmailType::class, array(
                'label' => 'Avsender',
            ))
            ->add('to', EmailType::class, array(
                'label' => 'Mottaker',
            ))

            ->add('datetime', DateTimeType::class, array(
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy HH:mm',
                'label' => 'Tidspunkt',
                'attr' => array('placeholder' => 'Klikk for å velge tidspunkt'),
            ))

            ->add('room', TextType::class, array(
                'label' => 'Rom',
            ))
            ->add('mapLink', TextType::class, array(
                'label' => false,
                'required' => false,
            ))
            ->add('campus', TextType::class, array(
                'label' => 'Campus',
                'required' => false,
            ))
            ->add('message', TextareaType::class, array(
                'label' => 'Melding',
                'attr' => array('rows' => '5'),
            ))
            ->add('saveAndSend', SubmitType::class, array(
                'label' => 'Send invitasjon på sms og e-post',
            ))
            ->add('preview', SubmitType::class, array(
                'label' => 'Forhåndsvis'
            ))
        ;
    }

    public function getBlockPrefix()
    {
        return 'scheduleInterview';
    }
}

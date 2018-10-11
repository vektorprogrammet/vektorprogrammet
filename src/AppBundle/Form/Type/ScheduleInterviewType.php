<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ScheduleInterviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('from', 'email', array(
                'label' => 'Avsender',
            ))
            ->add('to', 'email', array(
                'label' => 'Mottaker',
            ))
            ->add('datetime', 'datetime', array(
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy HH:mm',
                'label' => 'Tidspunkt',
                'attr' => array('placeholder' => 'Klikk for å velge tidspunkt'),
            ))
            ->add('room', 'text', array(
                'label' => 'Rom',
            ))
            ->add('mapLink', 'text', array(
                'label' => false,
                'required' => false,
            ))
            ->add('campus', 'text', array(
                'label' => 'Campus',
                'required' => false,
            ))
            ->add('message', 'textarea', array(
                'label' => 'Melding',
                'attr' => array('rows' => '5'),
            ))
            ->add('saveAndSend', 'submit', array(
                'label' => 'Send invitasjon på sms og e-post',
            ))
            ->add('preview', 'submit', array(
                'label' => 'Forhåndsvis'
            ))
        ;
    }

    public function getName()
    {
        return 'scheduleInterview';
    }
}

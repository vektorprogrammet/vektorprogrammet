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
                'label' => false,
                'widget' => 'single_text',
                'date_format' => 'yyyy-MM-dd  HH:mm:ss',
                'label' => 'Tidspunkt',
                'attr' => array('placeholder' => 'yyyy-MM-dd HH:mm:ss'),
            ))
            ->add('room', 'text', array(
                'label' => 'Rom',
            ))
            ->add('message', 'textarea', array(
                'label' => 'Melding',
                'attr' => array('rows' => '5'),
            ))
            ->add('save', 'submit', array(
                'label' => 'Lagre tidspunkt',
            ))
            ->add('saveAndSend', 'submit', array(
                'label' => 'Lagre tidspunkt og send mail',
            ));
    }

    public function getName()
    {
        return 'scheduleInterview';
    }
}

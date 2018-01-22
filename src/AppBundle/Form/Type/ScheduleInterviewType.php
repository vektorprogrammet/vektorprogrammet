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
                'label' => 'Link til rom på kart',
                'required' => false,
            ))
            ->add('message', 'textarea', array(
                'label' => 'Melding',
                'attr' => array('rows' => '5'),
            ))
            ->add('save', 'submit', array(
                'label' => 'Lagre tidspunkt',
                'attr' => array('style' => 'display:none')
            ))
            ->add('saveAndSend', 'submit', array(
                'label' => 'Lagre tidspunkt og send mail',
            ))
            ->add('preview', 'submit', array(
                'label' => 'Forhåndsvis'
            ))
            ->add('changeStatus', 'button', array(
                'label' => 'Endre status'
            ))
        ;
    }

    public function getName()
    {
        return 'scheduleInterview';
    }
}

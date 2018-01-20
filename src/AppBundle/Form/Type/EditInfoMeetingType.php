<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\InfoMeeting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditInfoMeetingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateTimeType::class, array(
                'label' => 'Dato og klokkeslett',
                'format' => 'dd.MM.yyyy HH:mm',
                'widget' => 'single_text'
            ))
            ->add('room', 'text', array(
                'label' => 'Rom',
                'attr' => array('placeholder' => 'S7')
            ))
            ->add('extra', 'text', array(
                'label' => 'Ekstra',
                'attr' => array('placeholder' => 'Det blir gratis pizza!')
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => InfoMeeting::class
        ));
    }
}

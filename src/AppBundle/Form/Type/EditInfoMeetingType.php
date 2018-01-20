<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\InfoMeeting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;

class EditInfoMeetingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('time', TimeType::class, array(
                'label' => 'Tid',
                'widget' => 'single_text',
            ))
            ->add('date', DateType::class, array(
                'label' => 'Dato',
                'widget' => 'single_text',
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

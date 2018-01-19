<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\InfoMeeting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditInfoMeetingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('time', 'text', array(
                'label' => 'Tid',
                'attr' => array('placeholder' => '16:15')
            ))
            ->add('date', 'text', array(
                'label' => 'Dato',
                'attr' => array('placeholder' => '30/01')
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

<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SupportTicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array(
            'label' => false,
            'attr' => array('placeholder' => 'Ditt navn'), ));
        $builder->add('email', 'email', array(
            'label' => false,
            'attr' => array('placeholder' => 'Din E-post'), ));
        $builder->add('subject', 'text', array(
            'label' => false,
            'attr' => array('placeholder' => 'Emne'), ));
        $builder->add('body', 'textarea', array(
            'label' => false,
            'attr' => array(
                'cols' => '5',
                'rows' => '9',
                'placeholder' => 'Melding',
            ),
        ));
        $builder->add('submit', 'submit', array('label' => 'Send melding'));
        $builder->add('captcha', 'captcha', array(
            'label' => ' ',
            'width' => 200,
            'height' => 50,
            'length' => 5,
            'quality' => 200,
            'keep_value' => true,
            'distortion' => false,
            'background_color' => [255, 255, 255],
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\SupportTicket',
        ));
    }

    public function getName()
    {
        return 'support_ticket';
    }
}

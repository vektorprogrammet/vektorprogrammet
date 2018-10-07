<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SponsorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'Sponsornavn',
            ))
            ->add('url', 'text', array(
                'label' => 'Sponsors hjemmeside',
            ))
            ->add('size', 'choice', array(
                'required' => true,
                'label' => 'Størrelse',
                'choices' => array(
                    'small' => 'Liten',
                    'medium' => 'Medium',
                    'large' => 'Stor',
                ),
                'multiple' => false,
                'expanded' => true,
            ))
            ->add('logoImagePath', 'file', array(
                'required' => false,
                'error_bubbling' => true,
                'data_class' => null,
                'label' => 'Last opp ny logo',
            ));
    }

    public function getName()
    {
        return 'sponsor';
    }
}

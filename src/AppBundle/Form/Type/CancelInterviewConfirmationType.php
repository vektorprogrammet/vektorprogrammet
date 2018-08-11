<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CancelInterviewConfirmationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('message', 'textarea', array(
                'label' => false,
                'attr' => array('rows' => '5'),
                'required' => false,
            ));
    }

    public function getName()
    {
        return 'CancelInterviewConfirmation';
    }
}

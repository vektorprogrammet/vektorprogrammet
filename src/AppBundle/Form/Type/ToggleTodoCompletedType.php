<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ToggleTodoCompletedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        $builder
            ->add('department', 'entity', array(
                'type' => 'hidden',
            ))
            ->add('semester', 'entity', array(
                'type' => 'hidden',
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Model\TodoCompleted',
        ));
    }

    public function getName()
    {
        return 'getTodoCompletedStatus';
    }
}

<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ModifySubstituteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('days', new DaysType(), array(
            'data_class' => 'AppBundle\Entity\Application',
        ));

        $builder->add('english', 'choice', array(
            'label' => 'Kan undervise på engelsk',
            'choices' => array(
                0 => 'Nei',
                1 => 'Ja',
            ),
            'expanded' => true,
            'multiple' => false,
        ));

        // Hvordan bruke data fra andre klasser enn application?
        // Her trengs blant annet user->phone
        /*
        $builder->add('phone', 'text', array(
            'data_class' => 'AppBundle\Entity\User',
            'label' => 'Telefon',
        ));
        */
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Application',
        ));
    }

    public function getName()
    {
        return 'application';
    }
}

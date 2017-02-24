<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ApplicationPracticalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('days', new DaysType(), array(
            'data_class' => 'AppBundle\Entity\Application',
        ));

        $builder->add('doublePosition', 'choice', array(
            'label' => 'Kunne du tenke deg dobbel stilling? Altså en gang i uka i 8 uker?',
            'choices' => array(
                0 => 'Nei',
                1 => 'Ja',
            ),
            'expanded' => true,
            'multiple' => false,
        ));

        $builder->add('preferredGroup', 'choice', array(
            'label' => 'Har du et ønske om bolk?',
            'choices' => array(
                null => 'Ingen',
                'Bolk 1' => 'Bolk 1',
                'Bolk 2' => 'Bolk 2',
            ),
            'expanded' => true,
            'multiple' => false,
        ));

        $builder->add('english', 'choice', array(
            'label' => 'Vi har en internasjonal skole. Har du lyst til å undervise på engelsk?',
            'choices' => array(
                0 => 'Nei',
                1 => 'Ja',
            ),
            'expanded' => true,
            'multiple' => false,
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Application',
            'inherit_data' => true,
        ));
    }

    public function getName()
    {
        return 'application';
    }
}

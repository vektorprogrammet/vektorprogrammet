<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SchoolCapacityEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('monday', 'integer')
            ->add('tuesday', 'integer')
            ->add('wednesday', 'integer')
            ->add('thursday', 'integer')
            ->add('friday', 'integer')
            ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\SchoolCapacity',
        ));
    }

    public function getName()
    {
        return 'schoolCapacity';
    }
}

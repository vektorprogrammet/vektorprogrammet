<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DaysType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('monday', CheckboxType::class, array(
            'label' => 'Mandag',
            'required' => false,
        ));

        $builder->add('tuesday', CheckboxType::class, array(
            'label' => 'Tirsdag',
            'required' => false,
        ));

        $builder->add('wednesday', CheckboxType::class, array(
            'label' => 'Onsdag',
            'required' => false,
        ));

        $builder->add('thursday', CheckboxType::class, array(
            'label' => 'Torsdag',
            'required' => false,
        ));

        $builder->add('friday', CheckboxType::class, array(
            'label' => 'Fredag',
            'required' => false,
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Application',
            'inherit_data' => true,
            'label' => '',
        ));
    }

    public function getName()
    {
        return 'application';
    }
}

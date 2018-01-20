<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ApplicationExistingUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('applicationPractical', new ApplicationPracticalType(), array(
            'data_class' => 'AppBundle\Entity\Application',
        ));
        $builder->add('save', 'submit', array('label' => 'Søk nå!'));
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

<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CreateUserOnApplicationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', 'text', array(
                'label' => 'Fornavn',
            ))
            ->add('lastName', 'text', array(
                'label' => 'Etternavn',
            ))
            ->add('phone', 'text', array(
                'label' => 'Telefon',
            ))
            ->add('email', 'email', array(
                'label' => 'E-post',
            ))
            ->add('fieldOfStudy', 'entity', array(
                'label' => 'Linje',
                'class' => 'AppBundle:FieldOfStudy',
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
        ));
    }

    public function getName()
    {
        return 'createUser';
    }
}

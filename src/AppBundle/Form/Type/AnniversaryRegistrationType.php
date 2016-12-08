<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AppBundle\Entity\User;

class AnniversaryRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', 'text', array('label' => 'Fornavn'))
            ->add('lastname', 'text', array('label' => 'Etternavn'))
            ->add('email', 'email', array('label' => 'E-post'))
            ->add('phone', 'text', array('label' => 'Tlf'))
            ->add('allergies', 'textarea', array(
                'label' => 'Allergier',
                'required' => false,
                'attr' => array(
                    'cols' => 30,
                    'rows' => 5,
                ),
            ))
            ->add('save', 'submit', array('label' => 'Registrer'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\AnniversaryRegistration',
            'user' => null,
        ));
    }

    public function getName()
    {
        return 'anniversaryRegistration'; // This must be unique
    }
}

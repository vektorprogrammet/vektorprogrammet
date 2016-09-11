<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class NewUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user_name', 'text', array(
                'label' => 'Brukernavn',
            ))
            ->add('password', 'repeated', array(
                'type' => 'password',
                'first_options' => array('label' => 'Passord'),
                'second_options' => array('label' => 'Gjenta passord'),
                'invalid_message' => 'Passordene må være like',
                'constraints' => array(new Assert\Length(array(
                    'min' => 8,
                    'max' => 64,
                    'minMessage' => 'Passordet må være på minst {{ limit }} tegn',
                    'groups' => array('username'),
                ))),
            ))
            ->add('save', 'submit', array(
                'label' => 'Opprett bruker', ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
        ));
    }

    public function getName()
    {
        return 'createNewUser';
    }
}

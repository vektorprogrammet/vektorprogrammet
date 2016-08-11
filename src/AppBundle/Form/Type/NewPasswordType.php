<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class NewPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', 'repeated', array(
                'type' => 'password',
                'first_options' => array('label' => 'Passord'),
                'second_options' => array('label' => 'Gjenta passord'),
                'constraints' => array(
                    new Assert\Length(array(
                        'min' => 8,
                        'minMessage' => 'Passordet må ha minst {{ limit }} tegn.',
                    )),
                ),
            ))
            ->add('save', 'submit', array(
                'label' => 'Lagre nytt passord', ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
        ));
    }

    public function getName()
    {
        return 'newPassword'; // This must be unique
    }
}

<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class EditUserPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', 'repeated', array(
                'first_name' => 'Passord',
                'second_name' => 'Gjenta_passord',
                'type' => 'password',
                'invalid_message' => 'Passordene må være like',
                'constraints' => array(
                    new Assert\Length(array(
                        'min' => 8,
                        'max' => 64,
                        'minMessage' => 'Passordet må være på minst {{ limit }} tegn',
                        'maxMessage' => 'Passordet må være mindre enn {{ limit }} tegn langt',
                    )),
                    new Assert\NotBlank(array(
                        'message' => 'Dette feltet kan ikke være tomt'
                    )),
                ),
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
        return 'editUserPassword';
    }
}

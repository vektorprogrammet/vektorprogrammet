<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class EditUserPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class, array(
                'first_name' => 'Passord',
                'second_name' => 'Gjenta_passord',
                'type' => 'password',
                'invalid_message' => 'Passordene må være like',
                'constraints' => array(new Assert\Length(array(
                    'min' => 8,
                    'max' => 64,
                    'minMessage' => 'Passordet må være på minst {{ limit }} tegn',
                    'maxMessage' => 'Passordet må være mindre enn {{ limit }} tegn langt',
                ))
            )));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
        ));
    }

    public function getBlockPrefix()
    {
        return 'editUserPassword';
    }
}

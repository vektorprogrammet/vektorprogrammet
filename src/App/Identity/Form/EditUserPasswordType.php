<?php

namespace App\Identity\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class EditUserPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class, [
                'first_name' => 'Passord',
                'second_name' => 'Gjenta_passord',
                'type' => PasswordType::class,
                'invalid_message' => 'Passordene må være like',
                'constraints' => [
                    new Assert\Length([
                        'min' => 8,
                        'max' => 64,
                        'minMessage' => 'Passordet må være på minst {{ limit }} tegn',
                        'maxMessage' => 'Passordet må være mindre enn {{ limit }} tegn langt',
                    ]),
                    new Assert\NotBlank([
                        'message' => 'Dette feltet kan ikke være tomt',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => \App\Identity\Infrastructure\Entity\User::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'editUserPassword';
    }
}

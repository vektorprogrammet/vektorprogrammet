<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class NewUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user_name', TextType::class, [
                'label' => 'Brukernavn',
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Passord'],
                'second_options' => ['label' => 'Gjenta passord'],
                'invalid_message' => 'Passordene må være like',
                'constraints' => [new Assert\Length([
                    'min' => 8,
                    'max' => 64,
                    'minMessage' => 'Passordet må være på minst {{ limit }} tegn',
                    'maxMessage' => 'Passordet må være på maks {{ limit }} tegn',
                    'groups' => ['username'],
                ])],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Opprett bruker', ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => \App\Entity\User::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'createNewUser';
    }
}

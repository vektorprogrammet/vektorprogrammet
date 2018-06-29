<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
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
            ->add('user_name', TextType::class, array(
                'label' => 'Brukernavn',
            ))
            ->add('password', RepeatedType::class, array(
                'type' => 'password',
                'first_options' => array('label' => 'Passord'),
                'second_options' => array('label' => 'Gjenta passord'),
                'invalid_message' => 'Passordene må være like',
                'constraints' => array(new Assert\Length(array(
                    'min' => 8,
                    'max' => 64,
                    'minMessage' => 'Passordet må være på minst {{ limit }} tegn',
                    'maxMessage' => 'Passordet må være på maks {{ limit }} tegn',
                    'groups' => array('username'),
                ))),
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Opprett bruker', ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
        ));
    }

    public function getBlockPrefix()
    {
        return 'createNewUser';
    }
}

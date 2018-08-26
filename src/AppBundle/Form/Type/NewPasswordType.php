<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;

class NewPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class, array(
                'type' => 'password',
                'first_options' => array('label' => 'Passord'),
                'second_options' => array('label' => 'Gjenta passord'),
                'constraints' => array(
                    new Assert\Length(array(
                        'min' => 8,
                        'minMessage' => 'Passordet må ha minst {{ limit }} tegn.',
                    )),
                    new NotBlank(),
                ),
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
        ));
    }

    public function getBlockPrefix()
    {
        return 'newPassword'; // This must be unique
    }
}

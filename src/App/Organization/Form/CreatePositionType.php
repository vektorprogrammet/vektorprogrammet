<?php

declare(strict_types=1);

namespace App\Organization\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreatePositionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Navn',
            ])
            ->add('Lagre', SubmitType::class, []);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => \App\Organization\Infrastructure\Entity\Position::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'createPosition';
    }
}

<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SponsorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Sponsornavn',
            ])
            ->add('url', TextType::class, [
                'label' => 'Sponsors hjemmeside',
            ])
            ->add('size', ChoiceType::class, [
                'required' => true,
                'label' => 'Størrelse',
                'choices' => [
                    'Liten' => 'small',
                    'Medium' => 'medium',
                    'Stor' => 'large',
                ],
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('logoImagePath', FileType::class, [
                'required' => false,
                'error_bubbling' => true,
                'data_class' => null,
                'label' => 'Last opp ny logo',
            ]);
    }

    public function getBlockPrefix()
    {
        return 'sponsor';
    }
}

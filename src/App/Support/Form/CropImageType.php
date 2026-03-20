<?php

namespace App\Support\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CropImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image', TextType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Klikk for å velge bilde'],
            ])
            ->add('largeCropData', HiddenType::class)
            ->add('mediumCropData', HiddenType::class)
            ->add('smallCropData', HiddenType::class)
            ->add('crop', SubmitType::class, [
                'label' => 'Crop & bruk',
            ]);
    }

    public function getBlockPrefix()
    {
        return 'crop';
    }
}

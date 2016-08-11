<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CropImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image', 'text', array(
                'label' => false,
                'attr' => array('placeholder' => 'Klikk for å velge bilde'),
            ))
            ->add('largeCropData', 'hidden')
            ->add('mediumCropData', 'hidden')
            ->add('smallCropData', 'hidden')
            ->add('crop', 'submit', array(
                'label' => 'Crop & bruk',
            ));
    }

    public function getName()
    {
        return 'crop';
    }
}

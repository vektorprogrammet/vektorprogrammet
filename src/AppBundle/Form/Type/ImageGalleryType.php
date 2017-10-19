<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ImageGalleryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array(
            'label' => 'Tittel',
            'required' => true,
            //'max_length' => 225
            ))
            ->add('description', TextareaType::class, array(
                'label' => 'Beskrivelse',
                'required' => true,
            ))
            ->add('referenceName', TextType::class, array(
                'label' => 'Referansenavn',
                'required' => true,
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ImageGallery',
        ));
    }

    public function getName()
    {
        return 'imageDescription';
    }
}

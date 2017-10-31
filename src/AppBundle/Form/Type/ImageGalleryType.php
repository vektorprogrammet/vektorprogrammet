<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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
            //'max_length' => 225
            ))
            ->add('description', TextareaType::class, array(
                'label' => 'Beskrivelse',
            ))
            ->add('referenceName', TextType::class, array(
                'label' => 'Referansenavn',
            ))
            ->add('filters', CollectionType::class, array(
                'label' => 'Filtere',
                'entry_type' => CheckboxType::class,
                'entry_options' => array(
                    'required' => false,
                ),
                'by_reference' => false,
                'allow_delete' => true,
                'allow_add' => true,
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

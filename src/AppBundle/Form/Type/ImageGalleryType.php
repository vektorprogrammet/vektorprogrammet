<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ImageGalleryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $allFilters = $options['all_filters'];

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
            ->add('filters', ChoiceType::class, array(
                'label' => 'Filtere',
                'choices' => $allFilters,
                'multiple' => true,
                'expanded' => true,

            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ImageGallery',
            'all_filters' => array(),
        ));
    }

    public function getName()
    {
        return 'imageGallery';
    }
}

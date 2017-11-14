<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UploadImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextType::class, array(
                'label' => 'Beskrivelse',
                ))
            ->add('uploadedFile', FileType::class, array(
                'label' => 'Last opp bilde',
                'data_class' => null,
                'attr' => array('class' => 'upload-gallery-image-hack'),
                'label_attr' => array('class' => 'button'),
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Image',
            'validation_groups' => array('image_gallery_upload', 'Image'),
        ));
    }

    public function getName()
    {
        return 'image';
    }
}

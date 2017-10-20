<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextType::class, array(
                'label' => 'Beskrivelse',
                ))
            ->add('path', FileType::class, array(
                'label' => 'Last opp bilde',
                'required' => $options['upload_required'],
                'data_class' => null,
                'attr' => array('class' => 'upload-gallery-image-hack'),
                'label_attr' => array('class' => 'button'),
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Image',
            'upload_required' => true,
        ));
    }

    public function getName()
    {
        return 'image';
    }
}

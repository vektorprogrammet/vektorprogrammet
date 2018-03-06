<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateLetterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'label' => 'Emne',
            ))
            ->add('preview', 'submit', array(
                'label' => 'Forhåndsvis',
            ))
            ->add('content', 'ckeditor', array(
                'required' => false,
                'config' => array(
                    'height' => 500,
                    'filebrowserBrowseRoute' => 'elfinder',
                    'filebrowserBrowseRouteParameters' => array('instance' => 'newsletter_editor'),
                ),
                'label' => 'Innhold',
                'attr' => array('class' => 'hide'), // Graceful loading, hides the textarea that is replaced by ckeditor
            ))
            ->add('excludeApplicants', 'checkbox', array(
                'label' => 'Ekskluder søkere',
                'required' => false,
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Letter',
        ));
    }

    public function getName()
    {
        return 'app_bundle_create_letter_type';
    }
}

<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CreateExecutiveBoardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'Navn',
            ))
            ->add('email', 'email', array(
                'label' => 'E-post (valgfritt)',
                'required' => false,
            ))
            ->add('shortDescription', 'text', array(
                'label' => ' ',
                'max_length' => 125,
                'required' => false,
            ))
            ->add('preview', 'submit', array(
                'label' => 'Forhåndsvis',
            ))
            ->add('description', 'ckeditor', array(
                'required' => false,
                'config' => array(
                    'height' => 500,
                    'filebrowserBrowseRoute' => 'elfinder',
                    'filebrowserBrowseRouteParameters' => array('instance' => 'team_editor'), ),
                'label' => 'Lang beskrivelse (valgfritt)',
                'attr' => array('class' => 'hide'), // Graceful loading, hides the textarea that is replaced by ckeditor
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ExecutiveBoard',
        ));
    }

    public function getName()
    {
        return 'createBoard';
    }
}

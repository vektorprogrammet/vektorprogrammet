<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CreateTeamType extends AbstractType
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
            ->add('description', 'ckeditor', array(
                'required' => false,
                'config' => array(
                    'height' => 500,
                    'filebrowserBrowseRoute' => 'elfinder',
                    'filebrowserBrowseRouteParameters' => array('instance' => 'article_editor'), ),
                'label' => 'Lang beskrivelse (valgfritt)',
                'attr' => array('class' => 'hide'), // Graceful loading, hides the textarea that is replaced by ckeditor
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Team',
        ));
    }

    public function getName()
    {
        return 'createTeam';
    }
}

<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'label' => false,
                'attr' => array('placeholder' => 'Fyll inn tittel her'),
            ))
            ->add('article', 'ckeditor', array(
                'config' => array(
                    'height' => 500,
                    'filebrowserBrowseRoute' => 'elfinder',
                    'filebrowserBrowseRouteParameters' => array('instance' => 'article_editor'), ),
                'label' => false,
                'attr' => array('class' => 'hide'), // Graceful loading, hides the textarea that is replaced by ckeditor
            ))
            ->add('departments', 'entity', array(
                'label' => 'Regioner',
                'class' => 'AppBundle:Department',
                'property' => 'shortName',
                'multiple' => true,
                'expanded' => true,
            ))
            ->add('imageLarge', 'text', array(
                'label' => 'Hovedbilde',
                'attr' => array('placeholder' => 'Klikk for å velge bilde'),
            ))
            ->add('imageMedium', 'text', array(
                'label' => 'Medium bilde',
                'attr' => array('placeholder' => 'Klikk for å velge bilde'),
            ))
            ->add('imageSmall', 'text', array(
                'label' => 'Lite bilde',
                'attr' => array('placeholder' => 'Klikk for å velge bilde'),
            ))
            ->add('sticky', 'checkbox', array(
                'required' => false,
            ))
            ->add('preview', 'submit', array(
                'label' => 'Forhåndsvis',
            ))
            ->add('publish', 'submit', array(
                'label' => 'Publiser',
            ));
    }

    public function getName()
    {
        return 'article';
    }
}

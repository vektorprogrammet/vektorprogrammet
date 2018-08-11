<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'label' => 'Tittel',
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
            ->add('sticky', 'checkbox', array(
                'required' => false,
            ))
            ->add('published', ChoiceType::class, array(
                'label' => 'Status',
                'choices' => [
                    0 => 'Kladd',
                    1 => 'Publisert',
                ]
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'allow_extra_fields' => true,
            'data_class' => 'AppBundle\Entity\Article'
        ]);
    }

    public function getName()
    {
        return 'article';
    }
}

<?php

namespace AppBundle\Form\Type;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array(
                'label' => 'Tittel',
                'attr' => array('placeholder' => 'Fyll inn tittel her'),
            ))
            ->add('article', CKEditorType::class, array(
                'config' => array(
                    'height' => 500,
                    'filebrowserBrowseRoute' => 'elfinder',
                    'filebrowserBrowseRouteParameters' => array('instance' => 'article_editor'), ),
                'label' => false,
                'attr' => array('class' => 'hide'), // Graceful loading, hides the textarea that is replaced by ckeditor
            ))
            ->add('departments', EntityType::class, array(
                'label' => 'Regioner',
                'class' => 'AppBundle:Department',
                'property' => 'shortName',
                'multiple' => true,
                'expanded' => true,
            ))
            ->add('sticky', CheckboxType::class, array(
                'required' => false,
            ))
            ->add('published', ChoiceType::class, array(
                'label' => 'Status',
                'choices' => [
	                'Kladd' => 0,
	                'Publisert' => 1,
                ]
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'allow_extra_fields' => true,
            'data_class' => 'AppBundle\Entity\Article'
        ]);
    }

    public function getBlockPrefix()
    {
        return 'article';
    }
}

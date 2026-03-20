<?php

namespace App\Content\Form;

use App\Entity\Department;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
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
            ->add('title', TextType::class, [
                'label' => 'Tittel',
                'attr' => ['placeholder' => 'Fyll inn tittel her'],
            ])
            ->add('article', CKEditorType::class, [
                'config' => [
                    'height' => 500,
                    'filebrowserBrowseRoute' => 'elfinder',
                    'filebrowserBrowseRouteParameters' => ['instance' => 'article_editor'], ],
                'label' => false,
                'attr' => ['class' => 'hide'], // Graceful loading, hides the textarea that is replaced by ckeditor
            ])
            ->add('departments', EntityType::class, [
                'label' => 'Regioner',
                'class' => Department::class,
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('sticky', CheckboxType::class, [
                'required' => false,
            ])
            ->add('published', ChoiceType::class, [
                'label' => 'Status',
                'choices' => [
                    'Kladd' => 0,
                    'Publisert' => 1,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'allow_extra_fields' => true,
            'data_class' => \App\Content\Infrastructure\Entity\Article::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'article';
    }
}

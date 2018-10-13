<?php

namespace AppBundle\Form\Type;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateExecutiveBoardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'label' => 'Navn',
            ))
            ->add('email', EmailType::class, array(
                'label' => 'E-post (valgfritt)',
                'required' => false,
            ))
            ->add('shortDescription', TextType::class, array(
                'label' => 'Kort beskrivelse',
                'required' => false,
            ))
            ->add('preview', SubmitType::class, array(
                'label' => 'Forhåndsvis',
            ))
            ->add('description', CKEditorType::class, array(
                'required' => false,
                'config' => array(
                    'height' => 500,
                    'filebrowserBrowseRoute' => 'elfinder',
                    'filebrowserBrowseRouteParameters' => array('instance' => 'team_editor'), ),
                'label' => 'Lang beskrivelse (valgfritt)',
                'attr' => array('class' => 'hide'), // Graceful loading, hides the textarea that is replaced by ckeditor
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ExecutiveBoard',
        ));
    }

    public function getBlockPrefix()
    {
        return 'createExecutiveBoard';
    }
}

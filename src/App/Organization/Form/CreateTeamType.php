<?php

declare(strict_types=1);

namespace App\Organization\Form;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateTeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Navn',
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-post',
            ])
            ->add('shortDescription', TextType::class, [
                'label' => 'Kort beskrivelse',
                'required' => false,
            ])
            ->add('preview', SubmitType::class, [
                'label' => 'Forhåndsvis',
            ])
            ->add('acceptApplication', CheckboxType::class, [
                'label' => 'Ta i mot søknader?',
                'required' => false,
            ])
            ->add('deadline', DateTimeType::class, [
                'label' => 'Søknadsfrist',
                'format' => 'dd.MM.yyyy HH:mm',
                'widget' => 'single_text',
                'html5' => false,
                'required' => false,
            ])
            ->add('active', CheckboxType::class, [
                'label' => 'Aktivt team',
                'required' => false,
            ])
            ->add('description', CKEditorType::class, [
                'required' => false,
                'config' => [
                    'height' => 500,
                    'filebrowserBrowseRoute' => 'elfinder',
                    'filebrowserBrowseRouteParameters' => ['instance' => 'team_editor'], ],
                'label' => 'Lang beskrivelse (valgfritt)',
                'attr' => ['class' => 'hide'], // Graceful loading, hides the textarea that is replaced by ckeditor
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => \App\Organization\Infrastructure\Entity\Team::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'createTeam';
    }
}

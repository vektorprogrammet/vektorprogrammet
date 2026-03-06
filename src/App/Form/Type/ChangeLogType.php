<?php

namespace App\Form\Type;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;

class ChangeLogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class, [
            'required' => true,
            'label' => false,
            'attr' => ['placeholder' => 'Fyll inn tittel til objektet', 'autocomplete' => 'off'],
        ])

        ->add('description', TextareaType::class, [
            'attr' => ['placeholder' => 'Beskriv endringen'],
        ])
        ->add('gitHubLink', UrlType::class, [
        ])
        ->add('date', DateTimeType::class, [
            'label' => 'Velg dato endringen blir gjort',
            'format' => 'dd.MM.yyyy HH:mm',
            'widget' => 'single_text',
            'html5' => false,
            'attr' => [
                'placeholder' => 'Klikk for å velge tidspunkt',
                'autocomplete' => 'off',
            ],
            'required' => true,
            'auto_initialize' => false,
        ])
        ->add('description', CKEditorType::class, [
            'required' => false,
            'config' => [
                'height' => 500,
                'filebrowserBrowseRoute' => 'elfinder',
                'filebrowserBrowseRouteParameters' => ['instance' => 'team_editor'], ],
            'label' => 'Lang beskrivelse (valgfritt)',
            'attr' => ['class' => 'hide'],
        ])

        ->add('save', SubmitType::class, [
            'label' => 'Lagre',
        ]);
    }
}

<?php

namespace AppBundle\Form\Type;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
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
        $builder->add('title', TextType::class, array(
                'required' => true,
                'label' => false,
                'attr' => array('placeholder' => 'Fyll inn tittel til objektet', 'autocomplete' => 'off'),
            ))

        ->add('description', TextAreaType::class, array(
            'attr' => array('placeholder' => "Beskriv endringen"),
            ))
        ->add('gitHubLink', UrlType::class, array(

            ))
        ->add('date', DateTimeType::class, array(
            'label' => 'Velg dato endringen blir gjort',
            'format' => 'dd.MM.yyyy HH:mm',
            'widget' => 'single_text',
            'attr' => [
                'placeholder' => 'Klikk for å velge tidspunkt',
                'autocomplete' => 'off',
            ],
            'required' => true,
            'auto_initialize' => false,

            ))
        ->add('description', CKEditorType::class, array(
        'required' => false,
        'config' => array(
            'height' => 500,
            'filebrowserBrowseRoute' => 'elfinder',
            'filebrowserBrowseRouteParameters' => array('instance' => 'team_editor'), ),
        'label' => 'Lang beskrivelse (valgfritt)',
        'attr' => array('class' => 'hide'),
            ))

        ->add('save', SubmitType::class, array(
            'label' => 'Lagre',
            ));
    }
}

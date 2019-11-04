<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;


class ParentCourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('speaker', TextType::class, array(
                'label' => 'Skriv fullt navn på foredragsholder',
                'required' => true,
                'attr' => array(
                    'autocomplete' => 'off',
                ),
            ))
            ->add('place', TextType::class, array(
                'label' => 'Skriv sted for kurset',
                'required' => true,
                'attr' => array(
                    'autocomplete' => 'off',
                ),
            ))
            ->add('date', DateTimeType::class, array(
                'label' => 'Skriv tidspunkt og dato',
                'required' => true,
                'format' => 'dd.MM.yyyy HH:mm',
                'widget' => 'single_text',
                'attr' => [
                    'placeholder' => 'Klikk for å velge tidspunkt',
                    'autocomplete' => 'off',
                ],
                'required' => true,
                'auto_initialize' => false,

            ))
            ->add('information', TextType::class, array(
                'label' => 'Skriv mer informasjon om kurset',
                'required' => true,
                'attr' => array(
                    'autocomplete' => 'off',
                ),
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Meld på!',
            ));
    }
}

<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ParentCourseType extends AbstractType
{
    private $speaker;
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('speaker', TextType::class, array(
                'label' => 'Skriv fullt navn på foredragsholder',
                'required' => true,
                'attr' => array(
                    'autocomplete' => 'off',
                ),
                'data' => $options['speaker']
            ))
            ->add('place', TextType::class, array(
                'label' => 'Skriv sted for kurset',
                'required' => true,
                'attr' => array(
                    'autocomplete' => 'off',
                ),
                'data' => $options['place']
            ))
            ->add('link', TextType::class, array(
                'label' => 'Lim inn full MazeMap-lenke for sted',
                'required' => true,
                'attr' => array(
                    'autocomplete' => 'off',
                ),
                'data' => $options['link']
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
                'data' => $options['date']

            ))
            ->add('information', TextType::class, array(
                'label' => 'Skriv mer informasjon om kurset',
                'required' => true,
                'attr' => array(
                    'autocomplete' => 'off',
                ),
                'data' => $options['info']
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Opprett',
            ));
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ParentCourse',
            'speaker' => '',
            'place' => "",
            'link' => "",
            'date' => "",
            'info' => ""
        ));
    }
}

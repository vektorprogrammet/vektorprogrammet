<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Entity\Repository\SemesterRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

// --- / /

class SocialEventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array(
                'label' => 'Tittel',
                'attr' => array('placeholder' => 'Fyll inn tittel til event'),
            ))
            ->add('description', TextareaType::class, array(
                'label' => 'Beskrivelse',
                'attr' => array(
                    'rows' => 3,
                    'placeholder' => "Beskrivelse av arragement"
                ),
            ))
            ->add('start_time', DateTimeType::class, array(
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy HH:mm',
                'label' => 'Starttid for arrangement',
                'attr' => array('placeholder' => 'Klikk for å velge tidspunkt'),
            ))
            ## TODO: MULIHET FOR IKKE Å HA TID


            ->add('end_time', DateTimeType::class, array(
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy HH:mm',
                'label' => 'Sluttid for arrangement',
                'attr' => array('placeholder' => 'Klikk for å velge tidspunkt'),
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Lagre',
            ))


            ////// ---------------------------------------- /////
            ->add('department', EntityType::class, array(
                'label' => 'Hvilken region skal arrangementet gjelde for?',
                'class' => 'AppBundle:Department',
                'placeholder' => 'Alle regioner',
                'empty_data' => null,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->orderBy('d.city', 'ASC');
                },
                'required' => false,
            ))
            ->add('semester', EntityType::class, array(
                'label' => 'Hvilket semester skal arrangementet gjelde for?',
                'class' => 'AppBundle:Semester',
                'placeholder' => 'Alle semestre fra og med nåværende',
                'query_builder' => function (SemesterRepository $sr) {
                    return $sr->queryForAllSemestersOrderedByAge();
                },
                'required' => false,
            ))
            ////// ---------------------------------------- /////
        ;

    }


}

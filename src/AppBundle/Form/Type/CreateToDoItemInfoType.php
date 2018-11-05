<?php

namespace AppBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use AppBundle\Entity\Department;

class CreateToDoItemInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        $builder

            ->add('priority', 'choice', array(
                'label' => 'Hva er dette punktets prioritet?',
                'choices' => array(
                    0 => '0 - Lav',
                    1 => '1',
                    2 => '2',
                    3 => '3',
                    4 => '4',
                    5 => '5 - Høy'
                ),
            ))
            ->add('isMandatory', 'checkbox', array(
                'label' => 'Dette punktet er påbudt',
                'required' => false,
            ))
            /*->add('deadlineDate', 'datetime', array(
                'label' => 'Hvis dette punktet har deadline, vennligst før inn. Hvis ikke, la være blank:',
                'required' => false,
                'widget' => calen
            ))*/
            ->add('deadlineDate', DateTimeType::class, array(
                'label' => 'Hvis dette punktet har deadline, vennligst før inn. Hvis ikke, la være blank:',
                'format' => 'dd.MM.yyyy HH:mm',
                'widget' => 'single_text',
                'attr' => [
                    'placeholder' => 'Klikk for å velge tidspunkt'
                ],
                'required' => false,
            ))
            ->add('title', 'text', array(
                'label' => 'Hva er denne sin tittel?',
            ))
            ->add('description', 'text', array(
                'label' => 'Beskrivelse av gjøremålet?',
            ))
            ->add('department', 'entity', array(
                'label' => 'Hvilken department skal denne gjelde for?',
                'class' => 'AppBundle:Department',
                'empty_value' => 'Alle departments',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->orderBy('d.city', 'ASC');
                },
                'required'=>false,
            ))
            ->add('semester', 'entity', array(
                'label' => 'Hvilket semester skal denne gjelde for?',
                'class' => 'AppBundle:Semester',
                'empty_value' => 'Alle semestre',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.semesterStartDate', 'DESC');
                },
                'required'=>false,
            ))/*
            ->add('save', 'submit', array(
                'label' => 'Opprett1',
            ))*/;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Model\ToDoItemInfo',
        ));
    }

    public function getName()
    {
        return 'createToDoItemInfo';
    }
}

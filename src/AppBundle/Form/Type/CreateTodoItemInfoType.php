<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Repository\SemesterRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CreateTodoItemInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        $builder

            ->add('priority', ChoiceType::class, array(
                'label' => 'Hva er gjøremålet sin prioritet?',
                'choices' => array(
                    '1 - Lav' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5 - Høy' => 5
                ),
                'required' => true,
            ))
            ->add('isMandatory', CheckboxType::class, array(
                'label' => 'Dette gjøremålet er påbudt',
                'required' => false,
            ))
            ->add('deadlineDate', DateTimeType::class, array(
                'label' => 'Hvis dette gjøremålet har deadline, vennligst før inn. Hvis ikke, la være blank:',
                'format' => 'dd.MM.yyyy HH:mm',
                'widget' => 'single_text',
                'attr' => [
                    'placeholder' => 'Klikk for å velge tidspunkt',
                    'autocomplete' => 'off',
                ],
                'required' => false,
                'auto_initialize' => false,
            ))
            ->add('title', TextType::class, array(
                'label' => 'Hva er gjøremålet sin tittel?',
            ))
            ->add('description', TextType::class, array(
                'label' => 'Beskrivelse av gjøremålet?',
            ))
            ->add('department', EntityType::class, array(
                'label' => 'Hvilken region skal gjøremålet gjelde for?',
                'class' => 'AppBundle:Department',
                'placeholder' => 'Alle regioner',
                'empty_data' => null,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->orderBy('d.city', 'ASC');
                },
                'required'=>false,
            ))
            ->add('semester', EntityType::class, array(
                'label' => 'Hvilket semester skal gjøremålet gjelde for?',
                'class' => 'AppBundle:Semester',
                'placeholder' => 'Alle semestre fra og med nåværende',
                'query_builder' => function (SemesterRepository $sr) {
                    return $sr->queryForAllSemestersOrderedByAge();
                },
                'required'=>false,
            ));
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Model\TodoItemInfo',
        ));
    }

    public function getName()
    {
        return 'createTodoItemInfo';
    }
}

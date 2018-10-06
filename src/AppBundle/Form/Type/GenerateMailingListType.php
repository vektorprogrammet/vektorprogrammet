<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Repository\DepartmentRepository;
use AppBundle\Entity\Repository\SemesterRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class GenerateMailingListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('semester', 'entity', array(
                'class' => 'AppBundle:Semester',
                'label' => 'Velg semester',
                'query_builder' => function (SemesterRepository $sr) {
                    return $sr->queryForAllSemestersOrderedByAge();
                },
                'required' => true,
            ))
            ->add('department', 'entity', array(
                'class' => 'AppBundle:Department',
                'label' => 'Velg region',
                'query_builder' => function (DepartmentRepository $dr) {
                    return $dr->queryForActive();
                },
                'required' => true,
            ))
            ->add('type', 'choice', array(
                'label' => 'Velg type',
                'choices' => array(
                    'Assistent' => 'Assistent',
                    'Team' => 'Team',
                    'Alle' => 'Alle',
                ),
                'required' => true,
            ))
            ->add('save', 'submit', array(
                'label' => 'Generer',
            ));
    }
}

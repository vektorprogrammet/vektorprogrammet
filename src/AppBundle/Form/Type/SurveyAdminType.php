<?php

namespace AppBundle\Form\Type;


use AppBundle\Entity\Repository\DepartmentRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;

class SurveyAdminType extends SurveyType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('department', EntityType::class, array(
        'label' => 'Region',
        'class' => 'AppBundle:Department',
        'placeholder' => 'Alle regioner',
        'empty_data' => null,
        'query_builder' => function (DepartmentRepository $er) {
            return $er->createQueryBuilder('Department')
                ->select('Department')
                ->where('Department.active = true');
        },
        'required' => false,
    ));

        parent::buildForm($builder, $options);



    }

}
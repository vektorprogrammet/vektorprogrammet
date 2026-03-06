<?php

namespace App\Form\Type;

use App\Entity\Department;
use App\Entity\Repository\DepartmentRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;

class SurveyAdminType extends SurveyType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('department', EntityType::class, [
            'label' => 'Region',
            'class' => Department::class,
            'placeholder' => 'Alle regioner',
            'empty_data' => null,
            'query_builder' => fn (DepartmentRepository $er) => $er->createQueryBuilder('Department')
                ->select('Department')
                ->where('Department.active = true'),
            'required' => false,
        ]);

        parent::buildForm($builder, $options);
    }
}

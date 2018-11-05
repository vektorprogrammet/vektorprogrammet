<?php
/**
 * Created by IntelliJ IDEA.
 * User: amir
 * Date: 05.11.18
 * Time: 20:20
 */

namespace AppBundle\Form\Type;


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

        return parent::buildForm($builder, $options);

    }

}
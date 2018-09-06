<?php
/**
 * Created by IntelliJ IDEA.
 * User: Amir Ahmed
 * Date: 06.09.2018
 * Time: 17:24
 */

namespace AppBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;

class SurveySchoolSpecificExecuteType extends SurveyExecuteType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $semester = $builder->getData()->getSurvey()->getSemester();
        $builder->add('school', 'entity', array(
            'label' => 'School',
            'placeholder' => 'Velg Skole',
            'class' => 'AppBundle:School',
            'query_builder' => function (EntityRepository $er) use ($semester) {
                return $er
                    ->createQueryBuilder('school')
                    ->join('school.assistantHistories', 'assistantHistories')
                    ->where('assistantHistories.semester = :semester')
                    ->orderBy('school.name', 'ASC')
                    ->setParameter('semester', $semester);
            },
        ));

        parent::buildForm($builder, $options);
    }
}

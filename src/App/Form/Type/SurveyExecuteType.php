<?php

namespace App\Form\Type;

use App\Entity\Department;
use App\Scheduling\Infrastructure\Entity\School;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SurveyExecuteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $semester = $builder->getData()->getSurvey()->getSemester();
        /** @var Department $department */
        $department = $builder->getData()->getSurvey()->getDepartment();
        $builder->add('school', EntityType::class, [
            'label' => 'School',
            'placeholder' => 'Velg Skole',
            'class' => School::class,
            'query_builder' => fn (EntityRepository $er) => $er
                ->createQueryBuilder('school')
                ->join('school.assistantHistories', 'assistantHistories')
                ->innerJoin(
                    'school.departments',
                    'department',
                    'WITH',
                    'department.id = :departmentId'
                )
                ->where('assistantHistories.semester = :semester')
                ->orderBy('school.name', 'ASC')
                ->setParameters([
                    'semester' => $semester,
                    'departmentId' => $department->getId()]),
        ]);
        $builder->add('surveyAnswers', CollectionType::class, ['entry_type' => SurveyAnswerType::class]);

        $builder->add('save', SubmitType::class, [
            'label' => 'Send inn',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => \App\Entity\SurveyTaken::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'surveyTaken';
    }
}

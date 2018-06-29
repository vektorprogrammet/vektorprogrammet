<?php

namespace AppBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SurveyExecuteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $semester = $builder->getData()->getSurvey()->getSemester();
        $builder->add('school', EntityType::class, array(
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
        $builder->add('surveyAnswers', CollectionType::class, array('type' => new SurveyAnswerType()));

        $builder->add('save', SubmitType::class, array(
            'label' => 'Send inn',
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\SurveyTaken',
        ));
    }

    public function getBlockPrefix()
    {
        return 'surveyTaken';
    }
}

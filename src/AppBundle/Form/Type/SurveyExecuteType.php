<?php

namespace AppBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SurveyExecuteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$department  $options["data"]->getSemester()->getDepartent();
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
        $builder->add('surveyAnswers', 'collection', array('type' => new SurveyAnswerType()));

        $builder->add('save', 'submit', array(
            'label' => 'Send inn',
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\SurveyTaken',
        ));
    }

    public function getName()
    {
        return 'surveyTaken';
    }
}

<?php

namespace AppBundle\Form\Type;

use AppBundle\AppBundle;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SurveyExecuteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$department = $options["data"]->getSemester()->getDepartent();
        $department = $builder->getData()->getSemester()->getDepartment();
        $builder->add('school', 'entity', array(
            'label' => 'School',
            'class' => 'AppBundle:School',
            'query_builder' => function(EntityRepository $er) use ($department){
                return $er
                    ->createQueryBuilder('s','d')
                    ->from('AppBundle:School','sc')
                    ->join('s.departments','d')
                    ->where('d = :department')
                    ->orderBy('s.name', 'ASC')
                    ->setParameter('department',$department);
            }
        ));
        $builder->add('surveyAnswers', 'collection', array('type' => new SurveyAnswerType()));;

        $builder->add('save', 'submit', array(
            'label' => 'Send inn',
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Survey',
        ));
    }

    public function getName()
    {
        return 'survey';
    }
}
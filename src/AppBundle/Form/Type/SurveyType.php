<?php

namespace AppBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SurveyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('semester', 'entity', array(
            'label' => 'Semester',
            'class' => 'AppBundle:Semester',
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('s')
                    ->where('s.admissionEndDate > :limit')
                    ->setParameter('limit', new \DateTime('now -1 year'))
                    ->orderBy('s.semesterStartDate', 'DESC');
            },
        ));

        $builder->add('name', 'text', array(
            'label' => false,
            'attr' => array('placeholder' => 'Fyll inn tittel til undersøkelse'),
        ));

        $builder->add('surveyQuestions', 'collection', array(
            'type' => new SurveyQuestionType(),
            'allow_add' => true,
            'allow_delete' => true,
            'prototype_name' => '__q_prot__',
        ));

        $builder->add('save', 'submit', array(
            'label' => 'Lagre',
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

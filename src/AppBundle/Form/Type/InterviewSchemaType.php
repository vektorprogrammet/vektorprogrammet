<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InterviewSchemaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array(
            'label' => false,
            'attr' => array('placeholder' => 'Fyll inn skjema tittel'),
        ));

        $builder->add('interviewQuestions', 'collection', array(
            'type' => new InterviewQuestionType(),
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
            'data_class' => 'AppBundle\Entity\InterviewSchema',
        ));
    }

    public function getName()
    {
        return 'interviewSchema';
    }
}

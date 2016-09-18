<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InterviewQuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('question', 'text', array(
            'label' => 'Spørsmål',
            'attr' => array('placeholder' => 'Fyll inn nytt spørsmål'),
        ));

        $builder->add('help', 'text', array(
            'label' => 'Hjelpetekst',
            'required' => false,
            'attr' => array('placeholder' => 'Fyll inn hjelpetekst'),
        ));

        $builder->add('type', 'choice', array(
            'choices' => array(
                'text' => 'Text',
                'radio' => 'Multiple choice',
                'check' => 'Checkboxes',
                'list' => 'Velg fra liste',
            ),
            'label' => 'Type',
        ));

        $builder->add('alternatives', 'collection', array(
            'type' => new InterviewQuestionAlternativeType(),
            'allow_add' => true,
            'allow_delete' => true,
            'prototype_name' => '__a_prot__',
            'by_reference' => false,
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\InterviewQuestion',
        ));
    }

    public function getName()
    {
        return 'interviewQuestion';
    }
}

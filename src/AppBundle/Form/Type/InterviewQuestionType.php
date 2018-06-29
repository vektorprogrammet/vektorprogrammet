<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InterviewQuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('question', TextType::class, array(
            'label' => 'Spørsmål',
            'attr' => array('placeholder' => 'Fyll inn nytt spørsmål'),
        ));

        $builder->add('help', TextType::class, array(
            'label' => 'Hjelpetekst',
            'required' => false,
            'attr' => array('placeholder' => 'Fyll inn hjelpetekst'),
        ));

        $builder->add('type', ChoiceType::class, array(
            'choices' => array(
                'text' => 'Text',
                'radio' => 'Multiple choice',
                'check' => 'Checkboxes',
                'list' => 'Velg fra liste',
            ),
            'label' => 'Type',
        ));

        $builder->add('alternatives', CollectionType::class, array(
            'type' => new InterviewQuestionAlternativeType(),
            'allow_add' => true,
            'allow_delete' => true,
            'prototype_name' => '__a_prot__',
            'by_reference' => false,
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\InterviewQuestion',
        ));
    }

    public function getBlockPrefix()
    {
        return 'interviewQuestion';
    }
}

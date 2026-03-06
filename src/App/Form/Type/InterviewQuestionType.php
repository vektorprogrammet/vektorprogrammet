<?php

namespace App\Form\Type;

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
        $builder->add('question', TextType::class, [
            'label' => 'Spørsmål',
            'attr' => ['placeholder' => 'Fyll inn nytt spørsmål'],
        ]);

        $builder->add('help', TextType::class, [
            'label' => 'Hjelpetekst',
            'required' => false,
            'attr' => ['placeholder' => 'Fyll inn hjelpetekst'],
        ]);

        $builder->add('type', ChoiceType::class, [
            'choices' => [
                'Text' => 'text',
                'Multiple choice' => 'radio',
                'Checkboxes' => 'check',
                'Velg fra liste' => 'list',
            ],
            'label' => 'Type',
        ]);

        $builder->add('alternatives', CollectionType::class, [
            'entry_type' => InterviewQuestionAlternativeType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'prototype_name' => '__a_prot__',
            'by_reference' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => \App\Entity\InterviewQuestion::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'interviewQuestion';
    }
}

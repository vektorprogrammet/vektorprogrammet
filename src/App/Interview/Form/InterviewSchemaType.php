<?php

namespace App\Interview\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InterviewSchemaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, [
            'label' => false,
            'attr' => ['placeholder' => 'Fyll inn skjema tittel'],
        ]);

        $builder->add('interviewQuestions', CollectionType::class, [
            'entry_type' => InterviewQuestionType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'prototype_name' => '__q_prot__',
        ]);

        $builder->add('save', SubmitType::class, [
            'label' => 'Lagre',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => \App\Interview\Infrastructure\Entity\InterviewSchema::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'interviewSchema';
    }
}

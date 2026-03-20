<?php

namespace App\Interview\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InterviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('interviewAnswers', CollectionType::class, ['entry_type' => InterviewAnswerType::class]);

        $builder->add('interviewScore', InterviewScoreType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => \App\Interview\Infrastructure\Entity\Interview::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'interview';
    }
}

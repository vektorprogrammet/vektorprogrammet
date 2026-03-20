<?php

namespace App\Interview\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InterviewQuestionAlternativeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('alternative', TextType::class, [
            'label' => false,
            'attr' => ['placeholder' => 'Fyll inn nytt alternativ'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => \App\Interview\Infrastructure\Entity\InterviewQuestionAlternative::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'interviewQuestionAlternative';
    }
}

<?php

namespace App\Survey\Form;

use App\Survey\Infrastructure\Entity\SurveyQuestionAlternative;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SurveyQuestionAlternativeType extends AbstractType
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
            'data_class' => SurveyQuestionAlternative::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'interviewQuestionAlternative';
    }
}

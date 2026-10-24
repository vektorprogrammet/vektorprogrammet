<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InterviewQuestionAlternativeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('alternative', TextType::class, array(
            'label' => false,
            'attr' => array('placeholder' => 'Fyll inn nytt alternativ'),
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\InterviewQuestionAlternative',
        ));
    }

    public function getBlockPrefix(): string
    {
        return 'interviewQuestionAlternative';
    }
}

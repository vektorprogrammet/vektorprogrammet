<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SurveyQuestionAlternativeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('alternative', TextType::class, array(
            'label' => false,
            'attr' => array('placeholder' => 'Fyll inn nytt alternativ'),
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\SurveyQuestionAlternative',
        ));
    }

    public function getBlockPrefix()
    {
        return 'interviewQuestionAlternative';
    }
}

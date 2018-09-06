<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SurveyExecuteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->baseBuildForm($builder, $options);
    }

    private function baseBuildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('surveyAnswers', 'collection', array('type' => new SurveyAnswerType()));

        $builder->add('save', 'submit', array(
            'label' => 'Send inn',
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\SurveyTaken',
        ));
    }

    public function getName()
    {
        return 'surveyTaken';
    }
}

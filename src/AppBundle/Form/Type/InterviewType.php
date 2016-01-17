<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class InterviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('applicationInfo', new ApplicationInfoPracticalType());

        $builder->add('interviewAnswers', 'collection', array('type' => new InterviewAnswerType()));

        $builder->add('interviewScore', new InterviewScoreType());

        $builder->add('save', 'submit', array(
            'label' => 'Lagre',
        ));
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Interview',
        ));
    }

    public function getName()
    {
        return 'interview';
    }
}
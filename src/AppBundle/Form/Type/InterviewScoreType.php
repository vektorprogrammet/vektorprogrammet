<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InterviewScoreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = array_combine(range(1,6),range('1','6'));

        $builder->add('explanatoryPower', 'choice', array(
            'choices' => $choices,
            'label' => 'Forklaringsevne'
        ));

        $builder->add('roleModel', 'choice', array(
            'choices' => $choices,
            'label' => 'Forbilde for ungdomsskoleelever'
        ));

        $builder->add('suitability', 'choice', array(
            'choices' => $choices,
            'label' => 'Egnethet'
        ));

        $builder->add('suitableAssistant', 'choice', array(
            'label' => 'Passer denne studenten til å være vektorassistent?',
            'choices' => array(
                "Ja" => "Ja",
                "Kanskje" => "Kanskje",
                "Nei" => "Nei"
            ),
            'expanded' => true,
            'multiple' => false
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\InterviewScore',
        ));
    }

    public function getName()
    {
        return 'interviewScore';
    }
}
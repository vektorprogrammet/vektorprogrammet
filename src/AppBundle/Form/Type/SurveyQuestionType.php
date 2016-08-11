<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SurveyQuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('question', 'text', array(
            'label' => 'Spørsmål',
            'attr' => array('placeholder' => 'Fyll inn nytt spørsmål'),
        ));

        $builder->add('help', 'text', array(
            'label' => 'Hjelpetekst',
            'required' => false,
            'attr' => array('placeholder' => 'Fyll inn hjelpetekst'),
        ));

        $builder->add('type', 'choice', array(
            'choices' => array(
                'text' => 'Text',
                'radio' => 'Multiple choice',
                'list' => 'Velg fra liste',
            ),
            'label' => 'Type',
        ));

        $builder->add('alternatives', 'collection', array(
            'type' => new SurveyQuestionAlternativeType(),
            'allow_add' => true,
            'allow_delete' => true,
            'prototype_name' => '__a_prot__',
            'by_reference' => false,
        ));

        $builder->add('optional', 'choice', array(
            'label' => 'Valgfritt',
            'expanded' => 'true',
            'choices' => array(
                '0' => 'Nei',
                '1' => 'Ja',
            ),
        ));

        /*
        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            $iq = $event->getData();
            $form = $event->getForm();

            $id =  null === $iq ? 0 : $iq->id;

            $form->add('question', 'text', array(
                'label' => $id,
                'attr' => array('placeholder' => 'Fyll inn nytt spørsmål'),
            ));
        });*/
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\SurveyQuestion',
        ));
    }

    public function getName()
    {
        return 'interviewQuestion';
    }
}

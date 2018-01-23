<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ModifySubstituteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $options['label'] = false;
        $builder->add('days', new DaysType(), array(
            'label' => 'Dager som passer',
            'data_class' => 'AppBundle\Entity\Application',
        ));
        $builder->add('user', new UserDataForSubstituteType(), array(
            'department' => $options['department'],
            'label' => false,
        ));

        $builder->add('yearOfStudy', 'text', array(
            'label' => 'År',
        ));

        $builder->add('language', 'choice', array(
            'label' => 'Ønsket undervisningsspråk',
            'choices' => array(
                'Norsk' => 'Norsk',
                'Engelsk' => 'Engelsk',
                'Norsk og engelsk' => 'Norsk og engelsk',
            ),
            'expanded' => true,
            'multiple' => false,
        ));

        $builder->add('save', 'submit', array(
            'label' => 'Oppdater',
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Application',
            'department' => null,
        ));
    }

    public function getName()
    {
        return 'modifySubstitute';
    }
}

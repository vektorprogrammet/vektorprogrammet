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
        $builder->add('user', new UserDataForSubstituteType(), array(
            'department' => $options['department'],
            'label' => false,
        ));

        $builder->add('yearOfStudy', 'text', array(
            'label' => 'År',
        ));

        $builder->add('english', 'choice', array(
            'label' => 'Kan undervise på engelsk',
            'choices' => array(
                0 => 'Nei',
                1 => 'Ja',
            ),
            'expanded' => true,
            'multiple' => false,
        ));

        $builder->add('days', new DaysType(), array(
            'data_class' => 'AppBundle\Entity\Application',
            'label' => 'Hvilke dager passer IKKE for deg?',
        ));

        $builder->add('save', 'submit', array(
            'label' => 'Endre vikar',
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
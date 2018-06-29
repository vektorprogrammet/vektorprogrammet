<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModifySubstituteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $options['label'] = false;
        $builder->add('days', DaysType::class, array(
            'label' => 'Dager som passer',
            'data_class' => 'AppBundle\Entity\Application',
        ));
        $builder->add('user', new UserDataForSubstituteType(), array(
            'department' => $options['department'],
            'label' => false,
        ));

        $builder->add('yearOfStudy', TextType::class, array(
            'label' => 'År',
        ));

        $builder->add('language', ChoiceType::class, array(
            'label' => 'Ønsket undervisningsspråk',
            'choices' => array(
                'Norsk' => 'Norsk',
                'Engelsk' => 'Engelsk',
                'Norsk og engelsk' => 'Norsk og engelsk',
            ),
            'expanded' => true,
            'multiple' => false,
        ));

        $builder->add('save', SubmitType::class, array(
            'label' => 'Oppdater',
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Application',
            'department' => null,
        ));
    }

    public function getBlockPrefix()
    {
        return 'modifySubstitute';
    }
}

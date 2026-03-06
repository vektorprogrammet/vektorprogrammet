<?php

namespace App\Form\Type;

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
        $builder->add('days', DaysType::class, [
            'label' => 'Dager som passer',
            'data_class' => \App\Entity\Application::class,
        ]);
        $builder->add('user', UserDataForSubstituteType::class, [
            'department' => $options['department'],
            'label' => false,
        ]);

        $builder->add('yearOfStudy', TextType::class, [
            'label' => 'År',
        ]);

        $builder->add('language', ChoiceType::class, [
            'label' => 'Ønsket undervisningsspråk',
            'choices' => [
                'Norsk' => 'Norsk',
                'Engelsk' => 'Engelsk',
                'Norsk og engelsk' => 'Norsk og engelsk',
            ],
            'expanded' => true,
            'multiple' => false,
        ]);

        $builder->add('save', SubmitType::class, [
            'label' => 'Oppdater',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => \App\Entity\Application::class,
            'department' => null,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'modifySubstitute';
    }
}

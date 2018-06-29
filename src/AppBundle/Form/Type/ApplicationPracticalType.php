<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicationPracticalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('days', DaysType::class, array(
            'label' => 'Er det noen dager som IKKE passer for deg?',
            'data_class' => 'AppBundle\Entity\Application',
        ));

        $builder->add('yearOfStudy', ChoiceType::class, [
            'label' => 'Årstrinn',
            'choices' => [
                1 => '1',
                2 => '2',
                3 => '3',
                4 => '4',
                5 => '5',
            ],
        ]);

        $builder->add('doublePosition', ChoiceType::class, array(
            'label' => 'Kunne du tenke deg enkel eller dobbel stilling?',
            'choices' => array(
                0 => '4 uker',
                1 => '8 uker'
            ),
            'expanded' => true,
            'multiple' => false,
        ));

        $builder->add('preferredGroup', ChoiceType::class, array(
            'label' => 'Er det noen tidspunkt i løpet av semesteret du ikke kan delta på?',
            'choices' => array(
                null => 'Kan hele semesteret',
                'Bolk 2' => 'Kan ikke i bolk 1',
                'Bolk 1' => 'Kan ikke i bolk 2',
            ),
            'expanded' => true,
            'multiple' => false,
        ));

        $builder->add('language', ChoiceType::class, array(
            'label' => 'Vil du undervise på norsk skole eller internasjonal skole?',
            'choices' => array(
                'Norsk' => 'Norsk',
                'Engelsk' => 'Engelsk',
                'Norsk og engelsk' => 'Norsk og engelsk',
            ),
            'data' => 'Norsk',
            'expanded' => true,
            'multiple' => false,
        ));

        $builder->add('teamInterest', ChoiceType::class, array(
            'label' => 'Legg til personen i teaminteresse-listen?',
            'choices' => array(
                0 => 'Nei',
                1 => 'Ja',
            ),
            'expanded' => true,
            'multiple' => false,
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Application',
            'inherit_data' => true,
        ));
    }

    public function getBlockPrefix()
    {
        return 'application';
    }
}

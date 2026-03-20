<?php

namespace App\Admission\Form;

use App\Entity\Team;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicationPracticalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('days', DaysType::class, [
            'label' => 'Er det noen dager som IKKE passer for deg?',
            'data_class' => \App\Admission\Infrastructure\Entity\Application::class,
        ]);

        $builder->add('yearOfStudy', ChoiceType::class, [
            'label' => 'Årstrinn',
            'choices' => [
                '1. klasse' => '1. klasse',
                '2. klasse' => '2. klasse',
                '3. klasse' => '3. klasse',
                '4. klasse' => '4. klasse',
                '5. klasse' => '5. klasse',
            ],
        ]);

        $builder->add('doublePosition', ChoiceType::class, [
            'label' => 'Kunne du tenke deg enkel eller dobbel stilling?',
            'choices' => [
                '4 uker' => 0,
                '8 uker' => 1,
            ],
            'expanded' => true,
            'multiple' => false,
        ]);

        $builder->add('preferredGroup', ChoiceType::class, [
            'label' => 'Er det noen tidspunkt i løpet av semesteret du ikke kan delta på?',
            'choices' => [
                'Kan hele semesteret' => '',
                'Kan ikke i bolk 1' => 'Bolk 2',
                'Kan ikke i bolk 2' => 'Bolk 1',
            ],
            'expanded' => true,
            'multiple' => false,
        ]);

        $builder->add('language', ChoiceType::class, [
            'label' => 'Vil du undervise på norsk skole eller internasjonal skole?',
            'choices' => [
                'Norsk' => 'Norsk',
                'Engelsk' => 'Engelsk',
                'Norsk og engelsk' => 'Norsk og engelsk',
            ],
            'empty_data' => 'Norsk',
            'expanded' => true,
            'multiple' => false,
        ]);

        $builder->add('teamInterest', ChoiceType::class, [
            'label' => 'Legg til personen i teaminteresse-listen?',
            'choices' => [
                'Nei' => 0,
                'Ja' => 1,
            ],
            'expanded' => true,
            'multiple' => false,
        ]);

        $builder->add('potentialTeams', EntityType::class, [
            'label' => 'Hvilke team er du eventuelt interessert i?',
            'class' => Team::class,
            'query_builder' => fn (EntityRepository $entityRepository) => $entityRepository->createQueryBuilder('c'),
            'choices' => $options['teams'],
            'expanded' => true,
            'multiple' => true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => \App\Admission\Infrastructure\Entity\Application::class,
            'inherit_data' => true,
            'teams' => null,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'application';
    }
}

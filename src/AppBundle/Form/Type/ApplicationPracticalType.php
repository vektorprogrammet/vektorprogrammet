<?php

namespace AppBundle\Form\Type;

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
        $builder->add('days', DaysType::class, array(
            'label' => 'Er det noen dager som IKKE passer for deg?',
            'data_class' => 'AppBundle\Entity\Application',
        ));

        $builder->add('yearOfStudy', ChoiceType::class, [
            'label' => 'Årstrinn',
            'choices' => array(
                '1. klasse' => '1. klasse',
                '2. klasse' => '2. klasse',
                '3. klasse' => '3. klasse',
                '4. klasse' => '4. klasse',
                '5. klasse' => '5. klasse',
            ),
        ]);

        $builder->add('doublePosition', ChoiceType::class, array(
            'label' => 'Kunne du tenke deg enkel eller dobbel stilling?',
            'choices' => array(
                '4 uker' => 0,
                '8 uker' => 1
            ),
            'expanded' => true,
            'multiple' => false,
        ));

        $builder->add('preferredGroup', ChoiceType::class, array(
            'label' => 'Er det noen tidspunkt i løpet av semesteret du ikke kan delta på?',
            'choices' => array(
                'Kan hele semesteret' => '',
                'Kan ikke i bolk 1' => 'Bolk 2',
                'Kan ikke i bolk 2' => 'Bolk 1',
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
            'empty_data' => 'Norsk',
            'expanded' => true,
            'multiple' => false,
        ));

        $builder->add('teamInterest', ChoiceType::class, array(
            'label' => 'Legg til personen i teaminteresse-listen?',
            'choices' => array(
                'Nei' => 0,
                'Ja' => 1,
            ),
            'expanded' => true,
            'multiple' => false,
        ));

        $builder->add('potentialTeams', EntityType::class, array(
            'label' => 'Hvilke team er du eventuelt interessert i?',
            'class' => 'AppBundle:Team',
            'query_builder' => function (EntityRepository $entityRepository) {
                return $entityRepository->createQueryBuilder('c');
            },
            'choices' => $options['teams'],
            'expanded' => true,
            'multiple' => true,
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Application',
            'inherit_data' => true,
            'teams' => null,
        ));
    }

    public function getBlockPrefix()
    {
        return 'application';
    }
}

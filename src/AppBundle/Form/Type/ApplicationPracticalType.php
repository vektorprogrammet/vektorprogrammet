<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ApplicationPracticalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('days', new DaysType(), array(
            'label' => 'Hvilke dager passer IKKE for deg?',
            'data_class' => 'AppBundle\Entity\Application',
        ));

        $builder->add('doublePosition', 'choice', array(
            'label' => 'Kunne du tenke deg dobbel stilling? Altså en gang i uka i 8 uker?',
            'choices' => array(
                0 => 'Nei',
                1 => 'Ja',
            ),
            'expanded' => true,
            'multiple' => false,
        ));

        $builder->add('preferredGroup', 'choice', array(
            'label' => 'Er det noen tidspunkt i løpet av semesteret du ikke kan delta på?',
            'choices' => array(
                null => 'Kan hele semesteret',
                'Bolk 2' => 'Kan ikke i bolk 1',
                'Bolk 1' => 'Kan ikke i bolk 2',
            ),
            'expanded' => true,
            'multiple' => false,
        ));

        $builder->add('english', 'choice', array(
            'label' => 'Vi har en internasjonal skole. Har du lyst til å undervise på engelsk?',
            'choices' => array(
                0 => 'Nei',
                1 => 'Ja',
            ),
            'expanded' => true,
            'multiple' => false,
        ));

        $builder->add('teamInterest', 'choice', array(
        'label' => 'Kan du tenke deg å være med i organiseringen av Vektorprogrammet? Dette inneholder teamarbeid',
        'choices' => array(
            0 => 'Nei',
            1 => 'Ja',
        ),
        'expanded' => true,
        'multiple' => false,
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Application',
            'inherit_data' => true,
        ));
    }

    public function getName()
    {
        return 'application';
    }
}

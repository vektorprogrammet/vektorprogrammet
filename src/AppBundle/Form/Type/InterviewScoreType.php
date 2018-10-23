<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InterviewScoreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = array_combine(range(1, 6), range('1', '6'));

        $builder->add('explanatoryPower', ChoiceType::class, array(
            'choices' => $choices,
            'label' => 'Forklaringsevne',
            'help' => 'Går på oppgaveløsing.',
        ));

        $builder->add('roleModel', ChoiceType::class, array(
            'choices' => $choices,
            'label' => 'Forbilde for ungdomsskoleelever',
            'help' => 'Kan personen inspirere til realfag? Kan personen formidle matematikk på en \'interessant\' måte. Er ikke \'typisk nerdete\'? Noen eleven kan se opp til.',
        ));

        $builder->add('suitability', ChoiceType::class, array(
            'choices' => $choices,
            'label' => 'Egnethet',
            'help' => 'Oppegående, utadvendt, kontaktsøkende, initavitagende',
        ));

        $builder->add('suitableAssistant', ChoiceType::class, array(
            'label' => 'Passer denne studenten til å være vektorassistent?',
            'choices' => array(
                'Ja' => 'Ja',
                'Kanskje' => 'Kanskje',
                'Nei' => 'Nei',
            ),
            'expanded' => true,
            'multiple' => false,
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\InterviewScore',
        ));
    }

    public function getBlockPrefix()
    {
        return 'interviewScore';
    }
}

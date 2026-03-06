<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicationInterviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('applicationPractical', ApplicationPracticalType::class, [
            'data_class' => \App\Entity\Application::class,
            'teams' => $options['teams'],
        ]);

        $builder->add('heardAboutFrom', ChoiceType::class, [
            'label' => 'Hvor hørte du om Vektorprogrammet?',
            'choices' => [
                'Blesting' => 'Blesting',
                'Stand' => 'Stand',
                'Infomail/nettsida/facebook etc' => 'Infomail/nettsida/facebook etc',
                'Bekjente' => 'Bekjente',
                'Bekjente i styret' => 'Bekjente i styret',
                'Plakater/flyers' => 'Plakater/Flyers',
                'Linjeforeningen (f.eks fadderukene)' => 'Linjeforeningen (f.eks fadderukene)',
            ],
            'expanded' => true,
            'multiple' => true,
        ]);

        $builder->add('specialNeeds', TextType::class, [
            'label' => 'Spesielle behov',
            'required' => false,
        ]);

        $builder->add('interview', InterviewType::class);

        $builder->add('save', SubmitType::class, [
            'label' => 'Lagre kladd',
        ]);

        $builder->add('saveAndSend', SubmitType::class, [
            'label' => 'Lagre og send kvittering',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => \App\Entity\Application::class,
            'teams' => null,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'application';
    }
}

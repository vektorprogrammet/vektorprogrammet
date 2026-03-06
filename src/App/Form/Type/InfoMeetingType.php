<?php

namespace App\Form\Type;

use App\Entity\InfoMeeting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InfoMeetingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('showOnPage', CheckboxType::class, [
                'label' => 'Vis infomøte på opptakssiden',
            ])
            ->add('date', DateTimeType::class, [
                'label' => 'Dato og klokkeslett',
                'format' => 'dd.MM.yyyy HH:mm',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'placeholder' => 'Klikk for å velge tidspunkt',
                ],
            ])
            ->add('room', TextType::class, [
                'label' => 'Rom',
            ])
            ->add('description', TextType::class, [
                'label' => 'Kort beskrivelse',
            ])
            ->add('link', TextType::class, [
                'label' => 'Link til event (f.eks. Facebook)',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => InfoMeeting::class,
        ]);
    }
}

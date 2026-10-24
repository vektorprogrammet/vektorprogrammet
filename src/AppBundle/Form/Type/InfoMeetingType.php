<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\InfoMeeting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InfoMeetingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('showOnPage', CheckboxType::class, array(
                'label' => 'Vis infomøte på opptakssiden',
            ))
            ->add('date', DateTimeType::class, array(
                'html5' => false,
                'label' => 'Dato og klokkeslett',
                'format' => 'dd.MM.yyyy HH:mm',
                'widget' => 'single_text',
                'attr' => [
                    'placeholder' => 'Klikk for å velge tidspunkt'
                ]
            ))
            ->add('room', TextType::class, array(
                'label' => 'Rom',
            ))
            ->add('description', TextType::class, array(
                'label' => 'Kort beskrivelse',
            ))
            ->add('link', TextType::class, array(
                'label' => 'Link til event (f.eks. Facebook)',
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => InfoMeeting::class
        ));
    }
}

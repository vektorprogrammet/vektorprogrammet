<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Feedback;

class FeedbackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array(
                'label' => 'Tittel',
                'attr' => array(
                    'placeholder' => 'Eks: Hvordan gjør jeg...',
                ),
            ))
            ->add('description', TextareaType::class, array(
                'label' => 'Beskrivelse',
                'attr' => array(
                    'placeholder' => 'Eks: Jeg prøver å...',
                ),
            ))
            ->add('type', ChoiceType::class, array(
                'label' => ' ',
                'expanded' => true,
                'multiple' => false,
                'choices' => [
                        'Spørsmål' =>           Feedback::TYPE_QUESTION,
                        'Feilmelding' =>        Feedback::TYPE_ERROR,
                        'Ny funksjonalitet' =>  Feedback::TYPE_FEATURE_REQUEST
                    ],
                'data' => 'question'

            ))
            ->add('Send inn', SubmitType::class);
    }
}

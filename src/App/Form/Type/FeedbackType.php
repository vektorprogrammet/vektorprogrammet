<?php

namespace App\Form\Type;

use App\Entity\Feedback;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class FeedbackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Tittel',
                'attr' => [
                    'placeholder' => 'Eks: Hvordan gjør jeg...',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Beskrivelse',
                'attr' => [
                    'placeholder' => 'Eks: Jeg prøver å...',
                ],
            ])
            ->add('type', ChoiceType::class, [
                'label' => ' ',
                'expanded' => true,
                'multiple' => false,
                'choices' => [
                    'Spørsmål' => Feedback::TYPE_QUESTION,
                    'Feilmelding' => Feedback::TYPE_ERROR,
                    'Ny funksjonalitet' => Feedback::TYPE_FEATURE_REQUEST,
                ],
                'data' => 'question',
            ])
            ->add('send_inn', SubmitType::class, ['label' => 'Send inn']);
    }
}

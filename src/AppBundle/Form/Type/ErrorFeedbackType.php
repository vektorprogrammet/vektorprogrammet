<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;
use AppBundle\Entity\Feedback;

class ErrorFeedbackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array(
                'label' => 'Tittel',
                'attr' => array(
                    'placeholder' => 'Eks: Feilmelding når jeg...',
                ),
            ))
            ->add('description', TextareaType::class, array(
                'label' => 'Beskrivelse av problemet',
                'attr' => array(
                    'placeholder' => 'Eks: Jeg får opp feilmelding når...',
                ),
            ))
            ->add('type', HiddenType::class, array(
                    'data' => Feedback::TYPE_ERROR
            ))
            ->add('recaptcha', EWZRecaptchaType::class, [
                    'label' => false,
                    'mapped' => false,
                    'constraints' => array(
                        new RecaptchaTrue()
                )])
            ->add('Send inn', SubmitType::class);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Feedback',
        ));
    }
}

<?php

namespace App\Admission\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdmissionSubscriberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'E-post',
                    'autocomplete' => 'off',
                ],
            ])
            ->add('infoMeeting', CheckboxType::class, [
                'label' => 'Send meg også påminnelse om neste infomøte.',
                'required' => false,
                'attr' => [
                    'checked' => true,
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Meld deg på interesseliste',
                'attr' => [
                    'class' => 'btn btn-success',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => \App\Admission\Infrastructure\Entity\AdmissionSubscriber::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_admission_subscriber_type';
    }
}

<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SponsorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, array(
                'label' => 'Sponsornavn',
            ))
            ->add('url', TextType::class, array(
                'label' => 'Sponsors hjemmeside',
            ))
            ->add('size', ChoiceType::class, array(
                'required' => true,
                'label' => 'Størrelse',
                'choices' => array(
                    'Liten' => 'small',
                    'Medium' => 'medium',
                    'Stor' => 'large',
                ),
                'multiple' => false,
                'expanded' => true,
            ))
            ->add('logoImagePath', FileType::class, array(
                'required' => false,
                'error_bubbling' => true,
                'data_class' => null,
                'label' => 'Last opp ny logo',
            ));
    }

    public function getBlockPrefix(): string
    {
        return 'sponsor';
    }
}

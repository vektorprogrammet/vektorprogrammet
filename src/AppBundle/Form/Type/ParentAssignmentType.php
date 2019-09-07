<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;


class ParentAssignmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('navn', TextType::class, array(
                'label' => 'Skriv fullt navn',
                'required' => true,
            ))
            ->add('epost', EmailType::class, array(
                'label' => 'Skriv Email',
                'required' => true,
            ))
            ->add('tidspunkt', DateTimeType::class, array(
                'label' => 'Skriv dato',
                'required' => true,
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Meld på!',
            ));
    }
}
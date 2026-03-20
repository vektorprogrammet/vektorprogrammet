<?php

namespace App\Scheduling\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchoolCapacityEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('monday', IntegerType::class)
            ->add('tuesday', IntegerType::class)
            ->add('wednesday', IntegerType::class)
            ->add('thursday', IntegerType::class)
            ->add('friday', IntegerType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => \App\Scheduling\Infrastructure\Entity\SchoolCapacity::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'schoolCapacity';
    }
}

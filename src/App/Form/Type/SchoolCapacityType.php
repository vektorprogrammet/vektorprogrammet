<?php

namespace App\Form\Type;

use App\Entity\Repository\SchoolRepository;
use App\Entity\School;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchoolCapacityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $department = $builder->getData()->getDepartment();
        $builder
            ->add('school', EntityType::class, array(
                'label' => 'Skole',
                'class' => School::class,
                'query_builder' => function (SchoolRepository $er) use ($department) {
                    return $er->findActiveSchoolsWithoutCapacity($department);
                },
            ))
            ->add('monday', IntegerType::class)
            ->add('tuesday', IntegerType::class)
            ->add('wednesday', IntegerType::class)
            ->add('thursday', IntegerType::class)
            ->add('friday', IntegerType::class)
            ->add('save', SubmitType::class, array(
                'label' => 'Lagre',
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\SchoolCapacity',
        ));
    }

    public function getBlockPrefix()
    {
        return 'schoolCapacity';
    }
}

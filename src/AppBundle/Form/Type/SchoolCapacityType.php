<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Repository\SchoolCapacityRepository;
use AppBundle\Entity\Repository\SchoolRepository;
use Doctrine\ORM\EntityRepository;
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
        $department = $builder->getData()->getSemester()->getDepartment();
        $builder
            ->add('school', EntityType::class, array(
                'label' => 'Skole',
                'class' => 'AppBundle:School',
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
            'data_class' => 'AppBundle\Entity\SchoolCapacity',
        ));
    }

    public function getBlockPrefix()
    {
        return 'schoolCapacity';
    }
}

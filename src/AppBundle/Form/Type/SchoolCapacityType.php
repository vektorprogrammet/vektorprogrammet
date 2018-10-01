<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Repository\SchoolCapacityRepository;
use AppBundle\Entity\Repository\SchoolRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SchoolCapacityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $department = $builder->getData()->getSemester()->getDepartment();
        $builder
            ->add('school', 'entity', array(
                'label' => 'Skole',
                'class' => 'AppBundle:School',
                'query_builder' => function (SchoolRepository $er) use ($department) {
                    return $er->findActiveSchoolsWithoutCapacity($department);
                },
            ))
            ->add('monday', 'integer')
            ->add('tuesday', 'integer')
            ->add('wednesday', 'integer')
            ->add('thursday', 'integer')
            ->add('friday', 'integer')
            ->add('save', 'submit', array(
                'label' => 'Lagre',
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\SchoolCapacity',
        ));
    }

    public function getName()
    {
        return 'schoolCapacity';
    }
}

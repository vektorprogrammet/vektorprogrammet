<?php

namespace AppBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SchoolCapacityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $department = $builder->getData()->getSemester()->getDepartment();
        $semester = $builder->getData()->getSemester();
        $builder
            ->add('school', 'entity', array(
                'label' => 'Skole',
                'class' => 'AppBundle:School',
                'query_builder' => function(EntityRepository $er) use($department, $semester) {
                    /*return $er->createQueryBuilder('s')
                        ->join('AppBundle:SchoolCapacity', 'osc')
                        ->join('s.departments', 'd')
                        ->join('osc.semester', 'sem')
                        ->where('sem = :semester')
                        ->andWhere('d =:department')
                        ->setParameter('semester', $semester)
                        ->setParameter('department', $department);*/
                    return $er->createQueryBuilder('s')
                        ->join('s.departments', 'd')
                        ->where('d = :department')
                        ->setParameter('department', $department);

                    /*$newSchools = $qb->select('s')
                        ->from('AppBundle:SchoolCapacity', 'nsc')
                        ->join('nsc.school', 's')
                        ->where($qb->expr()->eq('s',$oldSchools))
                        ->andWhere('ns.department = :department')
                        ->setParameter('department', $department);*/

                    return $oldSchools;

                }
            ))
            ->add('monday', 'integer')
            ->add('tuesday', 'integer')
            ->add('wednesday', 'integer')
            ->add('thursday', 'integer')
            ->add('friday', 'integer')
            ->add('save', 'submit',array(
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
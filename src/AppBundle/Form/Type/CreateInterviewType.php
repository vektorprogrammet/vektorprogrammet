<?php

namespace AppBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateInterviewType extends AbstractType
{
    protected $roles;

    protected $department;

    public function __construct($roles, $department)
    {
        $this->roles = $roles;
        $this->department = $department;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('interviewer', EntityType::class, array(
            'class' => 'AppBundle:User',
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                    ->select('u')
                    ->join('u.roles', 'r')
                    ->join('u.fieldOfStudy', 'f')
                    ->join('f.department', 'd')
                    ->where('r.role IN (:roles)')
                    //->andWhere('d.id = :department')
                    ->orderBy('u.firstName')
                    ->setParameter('roles', $this->roles);
            },
            'group_by' => 'fieldOfStudy.department.shortName',
        ));

        $builder->add('interviewSchema', EntityType::class, array(
            'class' => 'AppBundle:InterviewSchema',
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('i')
                    ->select('i')
                    ->orderBy('i.id', 'DESC');
            },
            'property' => 'name',
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Interview',
        ));
    }

    public function getBlockPrefix()
    {
        return 'interview';
    }
}

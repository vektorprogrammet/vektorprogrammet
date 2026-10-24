<?php

namespace AppBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateInterviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('interviewer', EntityType::class, array(
            'class' => 'AppBundle\Entity\User',
            'query_builder' => function (EntityRepository $er) use ($options) {
                return $er->createQueryBuilder('u')
                    ->select('u')
                    ->join('u.roles', 'r')
                    ->where('r.role IN (:roles)')
                    ->orderBy('u.firstName')
                    ->setParameter('roles', $options['roles']);
            },
            'group_by' => 'fieldOfStudy.department.city',
        ));

        $builder->add('interviewSchema', EntityType::class, array(
            'class' => 'AppBundle\Entity\InterviewSchema',
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('i')
                    ->select('i')
                    ->orderBy('i.id', 'DESC');
            },
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Interview',
            'roles' => [],
        ));
    }

    public function getBlockPrefix(): string
    {
        return 'interview';
    }
}

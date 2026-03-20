<?php

namespace App\Interview\Form;

use App\Interview\Infrastructure\Entity\InterviewSchema;
use App\Identity\Infrastructure\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateInterviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('interviewer', EntityType::class, [
            'class' => User::class,
            'query_builder' => fn (EntityRepository $er) => $er->createQueryBuilder('u')
                ->select('u')
                ->join('u.roles', 'r')
                ->where('r.role IN (:roles)')
                ->orderBy('u.firstName')
                ->setParameter('roles', $options['roles']),
            'group_by' => 'fieldOfStudy.department.city',
        ]);

        $builder->add('interviewSchema', EntityType::class, [
            'class' => InterviewSchema::class,
            'query_builder' => fn (EntityRepository $er) => $er->createQueryBuilder('i')
                ->select('i')
                ->orderBy('i.id', 'DESC'),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => \App\Interview\Infrastructure\Entity\Interview::class,
            'roles' => [],
        ]);
    }

    public function getBlockPrefix()
    {
        return 'interview';
    }
}

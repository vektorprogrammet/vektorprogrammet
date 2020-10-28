<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Repository\PositionRepository;
use AppBundle\Entity\Repository\SemesterRepository;
use AppBundle\Entity\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateTeamMembershipType extends AbstractType
{
    private $department;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->department = $options['department'];

        $builder
            ->add('user', EntityType::class, array(
                'label' => 'Bruker',
                'class' => 'AppBundle:User',
                'query_builder' => function (UserRepository $ur) {
                    return $ur->createQueryBuilder('u')
                        ->orderBy('u.firstName', 'ASC')
                        ->Join('u.fieldOfStudy', 'fos')
                        ->Join('fos.department', 'd')
                        ->where('u.fieldOfStudy = fos.id')
                        ->andWhere('fos.department = d')
                        ->andWhere('d = ?1')
                        ->setParameter(1, $this->department);
                },
            ))
            ->add('isTeamLeader', ChoiceType::class, array(
                'choices' => [
                    'Medlem' => false,
                    'Leder' => true,
                ],
                'expanded' => true,
                'label' => false,
            ))
            ->add('position', EntityType::class, array(
                'label' => 'Stillingstittel',
                'class' => 'AppBundle:Position',
                'query_builder' => function (PositionRepository $pr) {
                    return $pr->createQueryBuilder('p')
                        ->orderBy('p.name', 'ASC');
                },
            ))
            ->add('startSemester', EntityType::class, array(
                'label' => 'Start semester',
                'class' => 'AppBundle:Semester',
                'query_builder' => function (SemesterRepository $sr) {
                    return $sr->queryForAllSemestersOrderedByAge();
                },
            ))
            ->add('endSemester', EntityType::class, array(
                'label' => 'Slutt semester (Valgfritt)',
                'class' => 'AppBundle:Semester',
                'query_builder' => function (SemesterRepository $sr) {
                    return $sr->queryForAllSemestersOrderedByAge();
                },
                'required' => false,
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Legg til',
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\TeamMembership',
            'department' => null
        ));
    }

    public function getBlockPrefix()
    {
        return 'createTeamMembership';
    }
}

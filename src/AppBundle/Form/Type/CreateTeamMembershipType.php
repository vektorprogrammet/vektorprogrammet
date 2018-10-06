<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Repository\PositionRepository;
use AppBundle\Entity\Repository\SemesterRepository;
use AppBundle\Entity\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CreateTeamMembershipType extends AbstractType
{
    private $departmentId;

    public function __construct($departmentId)
    {
        $this->departmentId = $departmentId;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', 'entity', array(
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
                        ->setParameter(1, $this->departmentId);
                },
            ))
            ->add('isTeamLeader', ChoiceType::class, array(
                'choices' => [
                    false => 'Medlem',
                    true => 'Leder',
                ],
                'expanded' => true,
                'label' => false,
            ))
            ->add('position', 'entity', array(
                'label' => 'Stillingstittel',
                'class' => 'AppBundle:Position',
                'query_builder' => function (PositionRepository $pr) {
                    return $pr->createQueryBuilder('p')
                        ->orderBy('p.name', 'ASC');
                },
            ))
            ->add('startSemester', 'entity', array(
                'label' => 'Start semester',
                'class' => 'AppBundle:Semester',
                'query_builder' => function (SemesterRepository $sr) {
                    return $sr->queryForAllSemestersOrderedByAge();
                },
            ))
            ->add('endSemester', 'entity', array(
                'label' => 'Slutt semester (Valgfritt)',
                'class' => 'AppBundle:Semester',
                'query_builder' => function (SemesterRepository $sr) {
                    return $sr->queryForAllSemestersOrderedByAge();
                },
                'required' => false,
            ))
            ->add('save', 'submit', array(
                'label' => 'Opprett',
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\TeamMembership',
        ));
    }

    public function getName()
    {
        return 'createTeamMembership';
    }
}

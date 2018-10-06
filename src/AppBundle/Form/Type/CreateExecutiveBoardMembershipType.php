<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Repository\SemesterRepository;
use AppBundle\Entity\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CreateExecutiveBoardMembershipType extends AbstractType
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
                        ->Join('u.fieldOfStudy', 'fos')
                        ->Join('fos.department', 'd')
                        ->where('d = :department')
                        ->setParameter('department', $this->departmentId)
                        ->addOrderBy('u.firstName', 'ASC');
                },
                'choice_label' => function ($value, $key, $index) {
                    return $value->getFullName();
                },
            ))
            ->add('positionName', 'text', array(
                'label' => 'Stilling',
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
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ExecutiveBoardMembership',
        ));
    }

    public function getName()
    {
        return 'createExecutiveBoardMembership';
    }
}

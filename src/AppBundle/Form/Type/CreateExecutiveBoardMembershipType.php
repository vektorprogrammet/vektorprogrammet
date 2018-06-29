<?php

namespace AppBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ->add('user', EntityType::class, array(
                'label' => 'Bruker',
                'class' => 'AppBundle:User',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
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
            ->add('positionName', TextType::class, array(
                'label' => 'Stilling',
            ))
            ->add('startSemester', EntityType::class, array(
                'label' => 'Start semester',
                'class' => 'AppBundle:Semester',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.semesterStartDate', 'DESC')
                        ->join('s.department', 'd')
                        ->where('d.id = ?1')
                        ->setParameter(1, $this->departmentId);
                },
            ))
            ->add('endSemester', EntityType::class, array(
                'label' => 'Slutt semester (Valgfritt)',
                'class' => 'AppBundle:Semester',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.semesterStartDate', 'DESC')
                        ->join('s.department', 'd')
                        ->where('d.id = ?1')
                        ->setParameter(1, $this->departmentId);
                },
                'required' => false,
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ExecutiveBoardMembership',
        ));
    }

    public function getBlockPrefix()
    {
        return 'createExecutiveBoardMembership';
    }
}

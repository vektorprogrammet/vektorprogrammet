<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Semester;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

class AddCoInterviewerType extends AbstractType
{
    private $startDate;
    private $endDate;
    private $department;

    public function __construct(Semester $semester)
    {
        $this->startDate = $semester->getSemesterStartDate();
        $this->endDate = $semester->getSemesterEndDate();
        $this->department = $semester->getDepartment();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', 'entity', array(
                'label' => 'Teammedlemmer',
                'class' => 'AppBundle:User',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('user')
                        ->select('user')
                        ->join('user.workHistories', 'wh')
                        ->join('user.fieldOfStudy', 'fos')
                        ->where('fos.department = :department')
                        ->join('wh.startSemester', 'ss')
                        ->andWhere('ss.semesterStartDate <= :startDate')
                        ->leftJoin('wh.endSemester', 'se')
                        ->andWhere('wh.endSemester is NULL OR se.semesterEndDate >= :endDate')
                        ->setParameter('startDate', $this->startDate)
                        ->setParameter('endDate', $this->endDate)
                        ->setParameter('department', $this->department);
                },
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Legg til'
            ));
    }
}

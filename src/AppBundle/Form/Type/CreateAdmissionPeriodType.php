<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\Repository\SemesterRepository;
use AppBundle\Entity\Semester;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateAdmissionPeriodType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $semesters = array_map(function (AdmissionPeriod $admissionPeriod) {
            return $admissionPeriod->getSemester()->getId();
        }, $options['admissionPeriods']);

        $builder
            ->add('Semester', EntityType::class, array(
                'label' => 'Semester',
                'class' => Semester::class,
                'query_builder' => function (SemesterRepository $sr) use ($semesters) {
                    return $sr->queryForAllSemestersOrderedByAge()
                        ->where('Semester.id NOT IN (:Semesters)')
                        ->setParameter('Semesters', $semesters);
                },
            ))
            ->add('startDate', DateTimeType::class, array(
                'label' => 'Opptak starttidspunkt',
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy HH:mm',
            ))
            ->add('endDate', DateTimeType::class, array(
                'label' => 'Opptak sluttidspunkt',
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy HH:mm',
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Opprett',
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\AdmissionPeriod',
            'admissionPeriods' => []
        ));
    }

    public function getName()
    {
        return 'createAdmissionPeriodType';
    }
}

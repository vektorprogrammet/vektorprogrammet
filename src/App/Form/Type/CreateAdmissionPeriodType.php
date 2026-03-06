<?php

namespace App\Form\Type;

use App\Entity\AdmissionPeriod;
use App\Entity\Repository\SemesterRepository;
use App\Entity\Semester;
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
        $semesters = array_map(fn (AdmissionPeriod $admissionPeriod) => $admissionPeriod->getSemester()->getId(), $options['admissionPeriods']);

        $builder
            ->add('Semester', EntityType::class, [
                'label' => 'Semester',
                'class' => Semester::class,
                'query_builder' => fn (SemesterRepository $sr) => $sr->queryForAllSemestersOrderedByAge()
                    ->where('Semester.id NOT IN (:Semesters)')
                    ->setParameter('Semesters', $semesters),
            ])
            ->add('startDate', DateTimeType::class, [
                'label' => 'Opptak starttidspunkt',
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy HH:mm',
                'html5' => false,
            ])
            ->add('endDate', DateTimeType::class, [
                'label' => 'Opptak sluttidspunkt',
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy HH:mm',
                'html5' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Opprett',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AdmissionPeriod::class,
            'admissionPeriods' => [],
        ]);
    }

    public function getName()
    {
        return 'createAdmissionPeriodType';
    }
}

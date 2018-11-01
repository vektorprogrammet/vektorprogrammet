<?php

namespace AppBundle\Form\Type;

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
        $builder
            ->add('Semester', EntityType::class, array(
                'label' => 'Semester',
                'class' => Semester::class,
                'query_builder' => function (SemesterRepository $sr) {
                    return $sr->queryForAllSemestersOrderedByAge();
                },
            ))
            ->add('admissionStartDate', DateTimeType::class, array(
                'label' => 'Opptak starttidspunkt',
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy HH:mm',
            ))
            ->add('admissionEndDate', DateTimeType::class, array(
                'label' => 'Opptak sluttidspunkt',
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy HH:mm',
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Opprett',
            ));
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\AdmissionPeriod',
        ));
    }

    public function getName()
    {
        return 'createAdmissionPeriodType';
    }
}

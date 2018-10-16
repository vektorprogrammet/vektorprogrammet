<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateSemesterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $years = array();
        for ($i = 2012; $i <= intval(date('Y')) + 1; ++$i) {
            $years[] = $i;
        }
        $years = array_reverse($years);
        $years = array_combine($years, $years);

        $builder
            ->add('semesterTime', ChoiceType::class, array(
                'choices' => array('Vår' => 'Vår', 'Høst' => 'Høst'),
                'expanded' => true,
                'label' => 'Semester type',
                'required' => true,
            ))
            ->add('year', ChoiceType::class, array(
                'choices' => $years,
                'label' => 'År',
                'required' => true,
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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Semester',
        ));
    }

    public function getBlockPrefix()
    {
        return 'createSemester';
    }
}

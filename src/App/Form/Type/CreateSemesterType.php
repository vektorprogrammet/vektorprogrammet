<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateSemesterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $years = [];
        for ($i = 2012; $i <= intval(date('Y')) + 1; ++$i) {
            $years[] = $i;
        }
        $years = array_reverse($years);
        $years = array_combine($years, $years);

        $builder
            ->add('semesterTime', ChoiceType::class, [
                'choices' => ['Vår' => 'Vår', 'Høst' => 'Høst'],
                'expanded' => true,
                'label' => 'Semester type',
                'required' => true,
            ])
            ->add('year', ChoiceType::class, [
                'choices' => $years,
                'label' => 'År',
                'required' => true,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Opprett',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => \App\Entity\Semester::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'createSemester';
    }
}

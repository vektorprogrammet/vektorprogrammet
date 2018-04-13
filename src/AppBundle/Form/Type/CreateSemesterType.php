<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
            ->add('semesterTime', 'choice', array(
                'choices' => array('Vår' => 'Vår', 'Høst' => 'Høst'),
                'expanded' => true,
                'label' => 'Semester type',
                'required' => true,
            ))
            ->add('year', 'choice', array(
                'choices' => $years,
                'label' => 'År',
                'required' => true,
            ))
            ->add('admissionStartDate', 'datetime', array(
                'label' => 'Opptak starttidspunkt',
                'widget' => 'single_text',
                'date_format' => 'yyyy-MM-dd  HH:mm:ss',
                'attr' => array(
                    'placeholder' => 'yyyy-MM-dd HH:mm:ss',
                ),
            ))
            ->add('admissionEndDate', 'datetime', array(
                'label' => 'Opptak sluttidspunkt',
                'widget' => 'single_text',
                'date_format' => 'yyyy-MM-dd  HH:mm:ss',
                'attr' => array(
                    'placeholder' => 'yyyy-MM-dd HH:mm:ss',
                ),
            ))
            ->add('save', 'submit', array(
                'label' => 'Opprett',
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Semester',
        ));
    }

    public function getName()
    {
        return 'createSemester';
    }
}

<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class EditSemesterType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {

		$builder
            ->add('admission_start_date', 'datetime', array(
				'label' => 'Opptak starttidspunkt',
				'widget' => 'single_text',
				'date_format' => 'yyyy-MM-dd  HH:mm:ss',
				'attr' => array(
					'placeholder' => 'yyyy-MM-dd HH:mm:ss',
				),
			))
			->add('admission_end_date', 'datetime', array(
				'label' => 'Opptak sluttidspunkt',
				'widget' => 'single_text',
				'date_format' => 'yyyy-MM-dd  HH:mm:ss',
				'attr' => array(
					'placeholder' => 'yyyy-MM-dd HH:mm:ss',
				),
			))
			->add('save', 'submit', array(
				'label' => 'Endre',
			));
			

			
    }
	
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
	
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Semester',
        ));
		
    }
	
    public function getName() {
        return 'createSemester';
    }
	
}
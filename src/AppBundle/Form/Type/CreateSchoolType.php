<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CreateSchoolType extends AbstractType {
	
    public function buildForm(FormBuilderInterface $builder, array $options) {
		 
		$builder
            ->add('name', 'text', array(
				'label' => 'Navn',
			))
			->add('contactPerson', 'text', array(
				'label' => 'Kontaktperson',
			))
			->add('phone', 'text', array(
				'label' => 'Telefon',
			))
			->add('email', 'text', array(
				'label' => 'E-post',
			))
			->add('international', 'choice', array(
				'label' => 'Skolen er internasjonal',
				'choices' => array(
					true => 'Ja',
					false => 'Nei'
				),
				'expanded' => true,
				'multiple' => false
			))
			->add('save', 'submit', array(
				'label' => 'Opprett',
			));
    }

	public function setDefaultOptions(OptionsResolverInterface $resolver) {

        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\School',
        ));

    }
	
    public function getName() {
        return 'createSchool';
    }
	
}
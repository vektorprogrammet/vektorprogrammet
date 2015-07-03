<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CreatePositionType extends AbstractType {
	
    public function buildForm(FormBuilderInterface $builder, array $options) {
		 
		$builder
			->add('name', 'text', array(
				'label' => 'Navn',
			))
			->add('Lagre', 'submit', array(
				
			));
    }
	
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
	
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Position',
        ));
		
    }
	
    public function getName() {
        return 'createPosition';
    }
	
}
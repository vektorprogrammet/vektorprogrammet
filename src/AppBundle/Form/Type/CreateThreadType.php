<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CreateThreadType extends AbstractType {
	
    public function buildForm(FormBuilderInterface $builder, array $options) {
		 
		$builder
			->add('subject', 'text', array(
				'label' => 'Tittel',
			))
            ->add('text', 'textarea', array(
				'label' => 'Tekst',
				'attr' => array(
					'rows' => 10,
				),
			))
			->add('Lagre', 'submit', array(
				
			));
    }
	
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
	
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Thread',
        ));
		
    }
	
    public function getName() {
        return 'createThread';
    }
	
}
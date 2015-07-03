<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CreateTeamType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
		 
		$builder
            ->add('name', 'text', array(
				'label' => 'Navn',
			))
			->add('save', 'submit', array(
				'label' => 'Opprett',
			));
    }
	
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
	
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Team',
        ));
		
    }
	
    public function getName() {
        return 'createTeam';
    }
	
}
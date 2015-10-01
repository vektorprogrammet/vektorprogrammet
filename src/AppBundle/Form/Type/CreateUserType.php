<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CreateUserType extends AbstractType {
	
	private $departmentId;
	private $admin;

    public function __construct($departmentId, $admin)
    {
        $this->departmentId = $departmentId;
		$this->admin = $admin;
    }
	

    public function buildForm(FormBuilderInterface $builder, array $options) {
		 
		$builder
            ->add('firstName', 'text', array(
				'label' => 'Fornavn',
			))
            ->add('lastName', 'text', array(
				'label' => 'Etternavn',
			))
			->add('gender', 'choice',  array(
				'label' => 'Kjønn',
				'choices' => array(
					0 => 'Mann',
					1 => 'Dame'
				),
				))
			->add('phone', 'text',  array(
				'label' => 'Telefon',
			))
			->add('user_name', 'text',  array(
				'label' => 'Brukernavn',
			))
			->add('Password', 'password',  array(
				'label' => 'Passord',
			))
			->add('Email', 'text',  array(
				'label' => 'E-post',
			))
			->add('fieldOfStudy', 'entity', array(
				'label' => 'Linje',
				'class' => 'AppBundle:FieldOfStudy',
				'query_builder' => function(EntityRepository $er )  {
					return $er->createQueryBuilder('f')
					  ->orderBy('f.short_name', 'ASC')
					  ->where('f.department = ?1')
					  // Set the parameter to the department ID that the current user belongs to.
					  ->setParameter(1, $this->departmentId);
					}
					))
			 ->add('save', 'submit', array(
				'label' => 'Opprett',
			));
			
			if ($this->admin == 'superadmin') {
				$builder->add('role', 'choice',  array(
				'label' => 'Rolle',
				'mapped' => false,
				'choices' => array(
					0 => 'Assistent',
					1 => 'Team',
					2 => 'Admin',
				),
				));
			}
			elseif ($this->admin == 'admin') {
				$builder->add('role', 'choice',  array(
				'label' => 'Rolle',
				'mapped' => false,
				'choices' => array(
					0 => 'Assistent',
				),
				));
			}
    }
	
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
	
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
        ));
		
    }
	
    public function getName() {
        return 'createUser';
    }
	
}
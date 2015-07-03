<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\User;

class ApplicationType extends AbstractType {

	private $id;
	private $today;
	private $authenticated;
	
	public function __construct($id, $today, $authenticated)
    {
        $this->id = $id;
		$this->today = $today;
		$this->authenticated = $authenticated;
    }
	
    public function buildForm(FormBuilderInterface $builder, array $options){
		
		$user = $options['user'];
		
		$id = $this->id;
		$today = $this->today;
			
		// The fields that populare the form
        $builder
			->add('firstname', 'text',  array('label' => 'Fornavn'))
            ->add('lastname', 'text',  array('label' => 'Etternavn'))
			->add('email', 'text',  array('label' => 'E-post'))	
			->add('phone', 'text',  array('label' => 'Tlf'))
			->add('statistic', new ApplicationStatisticType($id, $today), array('label' => ' ') )
            ->add('save', 'submit', array('label' => 'Søk nå!'));
			
			/*
			See options for configuration here:
			https://github.com/Gregwar/CaptchaBundle
			*/
			if ( $this->authenticated == true ) {
				
			}
			else {
				$builder->add('captchaAdmission', 'captcha', array(
					'label' => ' ',
					'width' => 200,
					'height' => 50,
					'length' => 5,
					'quality' =>200,
					'keep_value' => true,
					'distortion' => false,
					'background_color' => [111, 206, 238],
				)); 
			}
    }
	
	

	
	
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
	
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Application',
			'user' => null,
        ));
		
    }
	
    public function getName()
    {
        return 'application'; // This must be unique
    }
}
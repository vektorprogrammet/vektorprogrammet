<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ExistingUserApplicationType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$workChoices = array(
			"Bra" => "Bra",
			"Ok" => "Ok",
			"Ikke" => "Ikke"
		);

		$builder->add('yearOfStudy', 'choice',  array(
			'label' => 'Årstrinn',
			'choices' => array(
				1 => '1',
				2 => '2',
				3 => '3',
				4 => '4',
				5 => '5',
			),
		));

		$builder->add('doublePosition', 'choice', array(
			'label' => 'Kunne du tenke deg dobbelt stilling? Altså en gang i uka i 8 uker?',
			'choices' => array(
				0 => "Nei",
				1 => "Ja",
			),
			'expanded' => true,
			'multiple' => false
		));

		$builder->add('preferredGroup', 'choice', array(
			'label' => 'Har du et ønske om bolk?',
			'choices' => array(
				null => "Begge passer bra",
				"Bolk 1" => "Bolk 1",
				"Bolk 2" => "Bolk 2",
			),
			'expanded' => true,
			'multiple' => false
		));

		$builder->add('monday', 'choice', array(
			'label' => 'Mandag',
			'choices' => $workChoices,
			'expanded' => true
		));

		$builder->add('tuesday', 'choice', array(
			'label' => 'Tirsdag',
			'choices' => $workChoices,
			'expanded' => true
		));

		$builder->add('wednesday', 'choice', array(
			'label' => 'Onsdag',
			'choices' => $workChoices,
			'expanded' => true
		));

		$builder->add('thursday', 'choice', array(
			'label' => 'Torsdag',
			'choices' => $workChoices,
			'expanded' => true
		));

		$builder->add('friday', 'choice', array(
			'label' => 'Fredag',
			'choices' => $workChoices,
			'expanded' => true
		));

		$builder->add('substitute', 'choice', array(
			'label' => 'Dersom du ikke får stillingen som vektorassistent, vil du da være vikar for andre som melder frafall?',
			'choices' => array(
				0 => "Nei",
				1 => "Ja",
			),
			'expanded' => true,
			'multiple' => false
		));

		$builder->add('english', 'choice', array(
			'label' => 'Er du komfortabel med engelsk?',
			'help' => 'Vi samarberider med den internasjonale skolen og hvis vi ikke for nok kvalifiserte utvekslingstudenter kunne du tenke deg å være på den internasjonale skolen?',
			'choices' => array(
				0 => "Nei",
				1 => "Ja",
			),
			'expanded' => true,
			'multiple' => false
		));


		$builder->add('save', 'submit', array('label' => 'Søk nå!'));
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'AppBundle\Entity\Application',
		));
	}

	public function getName()
	{
		return 'application';
	}
}
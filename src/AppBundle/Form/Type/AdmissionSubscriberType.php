<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdmissionSubscriberType extends AbstractType {
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder->add('email', EmailType::class, [
			'label' => 'e-post'
		]);
	}

	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults([
			'data_class' => 'AppBundle\Entity\AdmissionSubscriber'
		]);
	}

	public function getBlockPrefix() {
		return 'app_bundle_admission_subscriber_type';
	}
}

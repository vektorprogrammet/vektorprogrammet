<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CreateWorkHistoryType extends AbstractType {

	private $departmentId;
	
	public function __construct($departmentId)
    {
        $this->departmentId = $departmentId;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
		 
		$builder
			->add('user', 'entity', array(
				'label' => 'Bruker',
				'class' => 'AppBundle:User',
				'query_builder' => function(EntityRepository $er )  {
					return $er->createQueryBuilder('u')
					  ->orderBy('u.firstName', 'ASC')
					  ->Join('u.fieldOfStudy', 'fos')
					  ->Join('fos.department', 'd')
					  ->where('u.fieldOfStudy = fos.id')
					  ->andWhere('fos.department = d')
					  ->andWhere('d = ?1')
					  ->setParameter(1, $this->departmentId);
					}
			))
			->add('position', 'entity', array(
				'label' => 'Stilling',
				'class' => 'AppBundle:Position',
				'query_builder' => function(EntityRepository $er )  {
					return $er->createQueryBuilder('p')
					  ->orderBy('p.name', 'ASC');
					}
			))
			->add('startDate', 'datetime', array(
				'label' => 'Starttidspunkt',
				'widget' => 'single_text',
				'date_format' => 'yyyy-MM-dd  HH:mm:ss',
				'attr' => array(
					'placeholder' => 'yyyy-MM-dd HH:mm:ss',
				),
			))
			->add('endDate', 'datetime', array(
				'label' => 'Sluttidspunkt (Valgfritt)',
				'widget' => 'single_text',
				'date_format' => 'yyyy-MM-dd  HH:mm:ss',
				'required' => false ,
				'attr' => array(
					'placeholder' => 'yyyy-MM-dd HH:mm:ss',
				),
			))
			->add('save', 'submit', array(
				'label' => 'Opprett',
			));
    }
	
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
	
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\WorkHistory',
        ));
		
    }
	
    public function getName() {
        return 'createWorkHistory';
    }
	
}
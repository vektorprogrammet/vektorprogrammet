<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;



class ApplicationStatisticType extends AbstractType {
	
	private $id;
	private $today;
	
    public function buildForm(FormBuilderInterface $builder, array $options){
		
		
		// The fields that populate the form
        $builder
			/*
			You have to query the database to get the fields of studies that are associated with the selected department.
			*/
			->add('gender', 'choice',  array(
				'label' => 'Kjønn',
				'choices' => array(
					0 => 'Mann',
					1 => 'Dame'
				),
				))
			
			
			->add('fieldOfStudy', 'entity', array(
				'label' => 'Linje',
				'class' => 'AppBundle:FieldOfStudy',
				'query_builder' => function(EntityRepository $er )  {
					return $er->createQueryBuilder('f')
					  ->orderBy('f.short_name', 'ASC')
					  ->where('f.department = ?1')
					  ->setParameter(1, $this->id);
					}
					))
			
			
			->add('yearOfStudy', 'choice',  array(
				'label' => 'Årstrinn',
				'choices' => array(
					1 => '1',
					2 => '2',
					3 => '3',
					4 => '4',
					5 => '5',
				),
				))
			->add('previousParticipation', 'choice',  array(
				'label' => 'Tidligere deltagelse',
				'choices' => array(
					1 => 'Ja',
					0 => 'Nei'
				),
				))
			->add('semester', 'entity', array(
				'class' => 'AppBundle:Semester',
				'query_builder' => function(EntityRepository $er )  {
					return $er->createQueryBuilder('s')
					  ->where('s.admission_start_date < ?1')
					  ->andWhere('s.admission_end_date > ?1')
					  ->andWhere('s.department = ?2')
					  ->setParameter(1, $this->today)
					  ->setParameter(2, $this->id);
					}
			));

    }
	
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
	
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ApplicationStatistic',
        ));
		
    }
	
	public function __construct($id, $today)
	{
		$this->id = $id;
		$this->today = $today;
	}

	
    public function getName()
    {
        return 'applicationStatistic'; // This must be unique
    }
	
}
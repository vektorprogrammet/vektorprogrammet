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
			->add('startSemester', 'entity', array(
				'label' => 'Start semester',
				'class' => 'AppBundle:Semester',
				'query_builder' => function(EntityRepository $er ) {
					return $er->createQueryBuilder('s')
						->orderBy('s.semesterStartDate', 'DESC')
						->join('s.department', 'd')
						->where('d.id = ?1')
						->setParameter(1, $this->departmentId);
				}
			))
			->add('endSemester', 'entity', array(
				'label' => 'Slutt semester (Valgfritt)',
				'class' => 'AppBundle:Semester',
				'query_builder' => function(EntityRepository $er ) {
					return $er->createQueryBuilder('s')
						->orderBy('s.semesterStartDate', 'DESC')
						->join('s.department', 'd')
						->where('d.id = ?1')
						->setParameter(1, $this->departmentId);
				},
				'required' => false
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
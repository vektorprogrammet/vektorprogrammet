<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CreateAssistantHistoryType extends AbstractType {
	
	private $department;

    public function __construct($department)
    {
        $this->department = $department;
    }
	
    public function buildForm(FormBuilderInterface $builder, array $options) {
		 
		$builder
            ->add('Semester', 'entity', array(
				'label' => 'Semester',
				'class' => 'AppBundle:Semester',
				'query_builder' => function(EntityRepository $er )  {
					return $er->createQueryBuilder('s')
						->orderBy('s.semesterStartDate', 'DESC')
						->where('s.department = ?1')
						->setParameter(1, $this->department);
				}
			))
			->add('workdays', 'choice', array(
				'label' => 'Antall uker (4 ganger = 4 uker, 2 ganger i uken i 4 uker = 8 uker)',
				'choices' => array(
					'1'   => '1',
					'2'   => '2',
					'3'   => '3',
					'4'   => '4',
					'5'   => '5',
					'6'   => '6',
					'7'   => '7',
					'8'   => '8',
				),
			))
			->add('School', 'entity', array(
				'label' => 'Skole',
				'class' => 'AppBundle:School',
				'query_builder' => function(EntityRepository $er )  {
					return $er->createQueryBuilder('s')
						->orderBy('s.name', 'ASC')
						->JOIN('s.departments', 'd')
						// Since it is a many to many bidirectional relationship we have to use the MEMBER OF function
						->where(':department MEMBER OF s.departments')
						->setParameter('department', $this->department);
				}
			))
			->add('bolk', 'choice', array(
				'label' => 'Bolk',
				'choices' => array(
					'Bolk 1'   => 'Bolk 1',
					'Bolk 2'   => 'Bolk 2',
					'Bolk 1, Bolk 2'   => 'Bolk 1 og Bolk 2',
				),
			))
			->add('day', 'choice', array(
				'label' => 'Dag',
				'choices' => array(
					'Mandag' => 'Mandag',
					'Tirsdag' => 'Tirsdag',
					'Onsdag' => 'Onsdag',
					'Torsdag' => 'Torsdag',
					'Fredag' => 'Fredag'
				)
			))
			->add('save', 'submit', array(
				'label' => 'Opprett',
			));
    }
	
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
	
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\AssistantHistory',
        ));
		
    }
	
    public function getName() {
        return 'createAssistantHistory';
    }
	
}
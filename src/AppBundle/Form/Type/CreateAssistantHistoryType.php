<?php

namespace AppBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateAssistantHistoryType extends AbstractType
{
    private $department;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->department = $options['department'];
        $builder
            ->add('Semester', EntityType::class, array(
                'label' => 'Semester',
                'class' => 'AppBundle:Semester',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.semesterStartDate', 'DESC')
                        ->where('s.department = ?1')
                        ->setParameter(1, $this->department);
                },
            ))
            ->add('workdays', ChoiceType::class, array(
                'label' => 'Antall uker (4 ganger = 4 uker, 2 ganger i uken i 4 uker = 8 uker)',
                'choices' => array(
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                    '7' => '7',
                    '8' => '8',
                ),
            ))
            ->add('School', EntityType::class, array(
                'label' => 'Skole',
                'class' => 'AppBundle:School',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.name', 'ASC')
                        ->JOIN('s.departments', 'd')
                        // Since it is a many to many bidirectional relationship we have to use the MEMBER OF function
                        ->where(':department MEMBER OF s.departments')
                        ->andWhere('s.active = true')
                        ->setParameter('department', $this->department);
                },
            ))
            ->add('bolk', ChoiceType::class, array(
                'label' => 'Bolk',
                'choices' => array(
                    'Bolk 1' => 'Bolk 1',
                    'Bolk 2' => 'Bolk 2',
                    'Bolk 1 og Bolk 2' => 'Bolk 1, Bolk 2',
                ),
            ))
            ->add('day', ChoiceType::class, array(
                'label' => 'Dag',
                'choices' => array(
                    'Mandag' => 'Mandag',
                    'Tirsdag' => 'Tirsdag',
                    'Onsdag' => 'Onsdag',
                    'Torsdag' => 'Torsdag',
                    'Fredag' => 'Fredag',
                ),
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Opprett',
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\AssistantHistory',
            'department' => null,
        ));
    }

    public function getBlockPrefix()
    {
        return 'createAssistantHistory';
    }
}

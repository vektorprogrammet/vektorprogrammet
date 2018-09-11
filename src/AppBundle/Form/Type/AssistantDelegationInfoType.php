<?php
/**
 * Created by IntelliJ IDEA.
 * User: sigtot
 * Date: 08.09.18
 * Time: 23:48
 */

namespace AppBundle\Form\Type;


use AppBundle\Entity\Department;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class AssistantDelegationInfoType extends AbstractType
{
    private $department;

    public function __construct(Department $department)
    {
        $this->department = $department;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('School', EntityType::class, array(
                'label' => 'Skole',
                'class' => 'AppBundle:School',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.name', 'ASC')
                        ->JOIN('s.departments', 'd')
                        // Since it is a many to many bidirectional relationship we have to use the MEMBER OF function
                        ->where(':department MEMBER OF s.departments')
                        ->setParameter('department', $this->department);
                },
            ))
            ->add('Term', EntityType::class, array(
                'label' => 'Bolk',
                'class' => 'AppBundle:Term',
                'required' => false,
            ))
            ->add('doublePosition', ChoiceType::class, array(
                'label' => 'Dobbel stilling',
                'choices' => array(
                    'Ja' => true,
                    'Nei' => false,
                ),
            ))
            ->add('day', ChoiceType::class, array(
                'label' => 'Dag',
                'choices' => array(
                    'Mandag' => 1,
                    'Tirsdag' => 2,
                    'Onsdag' => 3,
                    'Torsdag' => 4,
                    'Fredag' => 5,
                ),
            ));
    }

    public function getName()
    {
        return 'assistantDelegationInfoType';
    }
}
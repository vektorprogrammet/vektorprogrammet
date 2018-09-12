<?php
/**
 * Created by IntelliJ IDEA.
 * User: sigtot
 * Date: 08.09.18
 * Time: 23:48
 */

namespace AppBundle\Form\Type;


use AppBundle\Entity\Department;
use AppBundle\Entity\Repository\UserRepository;
use AppBundle\Entity\School;
use AppBundle\Entity\Semester;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
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
            ->add('school', EntityType::class, array(
                'label' => 'Skole',
                'class' => 'AppBundle\Entity\School',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.name', 'ASC')
                        ->JOIN('s.departments', 'd')
                        // Since it is a many to many bidirectional relationship we have to use the MEMBER OF function
                        ->where(':department MEMBER OF s.departments')
                        ->setParameter('department', $this->department);
                },
            ))
            ->add('weekDay', ChoiceType::class, array(
                'label' => 'Ukedag',
                'choices' => array(
                    1 => 'Mandag',
                    2 => 'Tirsdag',
                    3 => 'Onsdag',
                    4 => 'Torsdag',
                    5 => 'Fredag',
                ),
            ))
            ->add('numDays', IntegerType::class, array(
                'label' => 'Antall arbeidsdager (Enkel stilling: 4 dager, dobbel stilling: 8 dager)'
            ))
            ->add('startingWeek', IntegerType::class, array(
                'label' => 'Ukenummer: Første uke assistenten sendes ut'
            ));
    }

    public function getName()
    {
        return 'assistantDelegationInfoType';
    }
}
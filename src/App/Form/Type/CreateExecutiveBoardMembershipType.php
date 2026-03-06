<?php

namespace App\Form\Type;

use App\Entity\Repository\SemesterRepository;
use App\Entity\Repository\UserRepository;
use App\Entity\Semester;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateExecutiveBoardMembershipType extends AbstractType
{
    private $departmentId;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->departmentId = $options['departmentId'];

        $builder
            ->add('user', EntityType::class, [
                'label' => 'Bruker',
                'class' => User::class,
                'query_builder' => fn (UserRepository $ur) => $ur->createQueryBuilder('u')
                    ->Join('u.fieldOfStudy', 'fos')
                    ->Join('fos.department', 'd')
                    ->where('d = :department')
                    ->setParameter('department', $this->departmentId)
                    ->addOrderBy('u.firstName', 'ASC'),
                'choice_label' => fn ($value, $key, $index) => $value->getFullName(),
            ])
            ->add('positionName', TextType::class, [
                'label' => 'Stilling',
            ])
            ->add('startSemester', EntityType::class, [
                'label' => 'Start semester',
                'class' => Semester::class,
                'query_builder' => fn (SemesterRepository $sr) => $sr->queryForAllSemestersOrderedByAge(),
            ])
            ->add('endSemester', EntityType::class, [
                'label' => 'Slutt semester (Valgfritt)',
                'class' => Semester::class,
                'query_builder' => fn (SemesterRepository $sr) => $sr->queryForAllSemestersOrderedByAge(),
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => \App\Entity\ExecutiveBoardMembership::class,
            'departmentId' => null,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'createExecutiveBoardMembership';
    }
}

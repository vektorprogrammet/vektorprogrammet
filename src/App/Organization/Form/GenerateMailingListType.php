<?php

declare(strict_types=1);

namespace App\Organization\Form;

use App\Organization\Infrastructure\Entity\Department;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Shared\Entity\Semester;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class GenerateMailingListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('semester', EntityType::class, [
                'class' => Semester::class,
                'label' => 'Velg semester',
                'query_builder' => fn (SemesterRepository $sr) => $sr->queryForAllSemestersOrderedByAge(),
                'required' => true,
            ])
            ->add('department', EntityType::class, [
                'class' => Department::class,
                'label' => 'Velg region',
                'query_builder' => fn (DepartmentRepository $dr) => $dr->queryForActive(),
                'required' => true,
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Velg type',
                'choices' => [
                    'Assistent' => 'Assistent',
                    'Team' => 'Team',
                    'Alle' => 'Alle',
                ],
                'required' => true,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Generer',
            ]);
    }
}

<?php

namespace App\Content\Form;

use App\Entity\Department;
use App\Shared\Repository\SemesterRepository;
use App\Entity\Role;
use App\Shared\Entity\Semester;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SocialEventType extends AbstractType
{
    private $department;
    private $semester;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->department = $options['department'];
        $this->semester = $options['semester'];

        $builder
            ->add('title', TextType::class, [
                'label' => 'Tittel',
                'attr' => ['placeholder' => 'Fyll inn tittel til event'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Beskrivelse',
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Beskrivelse av arragement',
                ],
            ])
            ->add('link', TextType::class, [
                'label' => 'Link til event (f.eks. Facebook)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'https://www.link-til-event.no',
                ],
            ])
            ->add('startTime', DateTimeType::class, [
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy HH:mm',
                'html5' => false,
                'label' => 'Starttid for arrangement',
                'attr' => [
                    'placeholder' => 'Klikk for å velge tidspunkt',
                    'autocomplete' => 'off',
                ],
            ])

            ->add('endTime', DateTimeType::class, [
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy HH:mm',
                'html5' => false,
                'label' => 'Sluttid for arrangement',
                'attr' => [
                    'placeholder' => 'Klikk for å velge tidspunkt',
                    'autocomplete' => 'off',
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Lagre',
            ])

            ->add('department', EntityType::class, [
                'label' => 'Hvilken region skal arrangementet gjelde for?',
                'class' => Department::class,
                'data' => $this->department,
                'query_builder' => fn (EntityRepository $er) => $er->createQueryBuilder('d')
                    ->orderBy('d.city', 'ASC'),
                'required' => true,
            ])
            ->add('semester', EntityType::class, [
                'label' => 'Hvilket semester skal arrangementet gjelde for?',
                'class' => Semester::class,
                'data' => $this->semester,
                'query_builder' => fn (SemesterRepository $sr) => $sr->queryForAllSemestersOrderedByAge(),
                'required' => true,
            ])
            ->add('role', EntityType::class, [
                'label' => 'Hvilke type brukere kan melde seg på arrangementet?',
                'class' => Role::class,
                'query_builder' => fn (EntityRepository $er) => $er->createQueryBuilder('r')
                    ->select('r')
                    ->where('r.role IN (:roles)')
                    ->orderBy('r.role')
                    ->setParameter('roles', ['ROLE_USER', 'ROLE_TEAM_MEMBER']),
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'department' => Department::class,
            'semester' => Semester::class,
        ]);
    }
}

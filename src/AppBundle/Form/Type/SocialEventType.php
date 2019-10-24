<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Entity\Repository\SemesterRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
            ->add('title', TextType::class, array(
                'label' => 'Tittel',
                'attr' => array('placeholder' => 'Fyll inn tittel til event'),
            ))
            ->add('description', TextareaType::class, array(
                'label' => 'Beskrivelse',
                'attr' => array(
                    'rows' => 3,
                    'placeholder' => "Beskrivelse av arragement"
                ),
            ))
            ->add('link', TextType::class, array(
                'label' => 'Link til event (f.eks. Facebook)',
                'required' => false,
                'attr' => array(
                    'placeholder' => "https://www.link-til-event.no"
                ),
            ))
            ->add('startTime', DateTimeType::class, array(
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy HH:mm',
                'label' => 'Starttid for arrangement',
                'attr' => array(
                    'placeholder' => 'Klikk for å velge tidspunkt',
                    'autocomplete' => 'off'
                    ),
            ))

            ->add('endTime', DateTimeType::class, array(
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy HH:mm',
                'label' => 'Sluttid for arrangement',
                'attr' => array(
                    'placeholder' => 'Klikk for å velge tidspunkt',
                    'autocomplete' => 'off'
                    ),
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Lagre',
            ))

            ->add('department', EntityType::class, array(
                'label' => 'Hvilken region skal arrangementet gjelde for?',
                'class' => 'AppBundle:Department',
                'data' => $this->department,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->orderBy('d.city', 'ASC');
                },
                'required' => true,
            ))
            ->add('semester', EntityType::class, array(
                'label' => 'Hvilket semester skal arrangementet gjelde for?',
                'class' => 'AppBundle:Semester',
                'data' => $this->semester,
                'query_builder' => function (SemesterRepository $sr) {
                    return $sr->queryForAllSemestersOrderedByAge();
                },
                'required' => true,
            ))
            ->add('role', EntityType::class, array(
                'label' => 'Hvilke type brukere kan melde seg på arrangementet?',
                'class' => 'AppBundle:Role',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('r')
                        ->select('r')
                        ->where('r.role IN (:roles)')
                        ->orderBy('r.role')
                        ->setParameter('roles', ['ROLE_USER', 'ROLE_TEAM_MEMBER']);
                },
                'required' => true,
            ))
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'department' => 'AppBundle\Entity\Department',
            'semester' => 'AppBundle\Entity\Semester',
        ));
    }
}

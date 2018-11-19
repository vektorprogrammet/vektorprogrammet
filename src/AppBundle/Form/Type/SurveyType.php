<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Repository\DepartmentRepository;
use AppBundle\Entity\Repository\SemesterRepository;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Utils\SemesterUtil;

class SurveyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('semester', EntityType::class, array(
            'label' => 'Semester',
            'class' => 'AppBundle:Semester',
            'query_builder' => function (SemesterRepository $sr) {
                return $sr->queryForAllSemestersOrderedByAge()
                    ->andWhere('Semester.year > :limit')
                    ->setParameter('limit', SemesterUtil::timeToYear(new \DateTime('now')) - 1);
            },
        ))

        ->add('name', TextType::class, array(
            'label' => false,
            'attr' => array('placeholder' => 'Fyll inn tittel til undersøkelse'),
        ))

        ->add('showCustomFinishPage', ChoiceType::class, [
            'label' => 'Sluttside som vises etter undersøkelsen er besvart.',
            'multiple' => false,
            'expanded' => true,
            'choices' => [
                'Standard' => false,
                'Tilpasset' => true,
            ]
        ])


        ->add('confidential', ChoiceType::class, array(
                'label' => 'Resultater kan leses av',
                'multiple' => false,
                'expanded' => true,
                'choices' => [
                    'Medlemmer og Ledere' => false,
                    'Kun Ledere' => true
                ]
            ))


        ->add('team_survey', ChoiceType::class, [
            'label' => 'Dette er en undersøkelse rettet mot teammedlem (popup)',
            'multiple' => false,
            'expanded' => true,
            'choices' => [
                'Ja' => true,
                'Nei' => false,
            ]

        ])

            ->add('showCustomPopUpMessage', ChoiceType::class, [
                'label' => 'Egen pop-up melding?',
                'multiple' => false,
                'expanded' => true,
                'choices' => [
                    'Ja' => true,
                    'Nei' => false,
                ]

            ])

            ->add('surveyPopUpMessage', CKEditorType::class, [
                'label' => 'Pop-up melding, vises kun hvis ja er valgt.',
            ])



        ->add('finishPageContent', CKEditorType::class, [
            'label' => 'Tilpasset sluttside. Vises kun hvis "Tilpasset" er valgt over.',
        ])

        ->add('surveyQuestions', CollectionType::class, array(
            'entry_type' => SurveyQuestionType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'prototype_name' => '__q_prot__',
        ))

        ->add('save', SubmitType::class, array(
            'label' => 'Lagre',
        ));
    }
    

    public function getBlockPrefix()
    {
        return 'survey';
    }
}

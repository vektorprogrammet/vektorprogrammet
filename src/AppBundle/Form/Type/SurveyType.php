<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Repository\SemesterRepository;
use AppBundle\Entity\Survey;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SurveyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('semester', EntityType::class, array(
            'label' => 'Semester',
            'class' => 'AppBundle:Semester',
            'query_builder' => function (SemesterRepository $sr) {
                return $sr->queryForAllSemestersOrderedByAge();
            },
        ))

        ->add('name', TextType::class, array(
            'label' => false,
            'attr' => array('placeholder' => 'Fyll inn tittel til undersøkelse'),
        ))



        ->add('confidential', ChoiceType::class, array(
                'label' => 'Resultater kan leses av',
                'multiple' => false,
                'expanded' => true,
                'choices' => [
                    'Medlemmer og Ledere' => false,
                    'Kun Ledere' => true
                ]
            ))


        ->add('targetAudience', ChoiceType::class, [
            'label' => 'Denne undersøkelsen er rettet mot:',
            'multiple' => false,
            'expanded' => true,
            'choices' => [
                'Skoler/Elever' => Survey::$SCHOOL_SURVEY,
                'Teammedlemmer' => Survey::$TEAM_SURVEY,
                'Assistenter' => Survey::$ASSISTANT_SURVEY,


            ]

        ])

            ->add('showCustomPopUpMessage', ChoiceType::class, [
                'label' => 'Egendefinert pop-up melding?',
                'multiple' => false,
                'expanded' => true,
                'choices' => [
                    'Ja' => true,
                    'Nei' => false,
                ]

            ])

            ->add('surveyPopUpMessage', TextType::class, [
                'label' => 'Egendefinert pop-up melding (vises kun hvis ja er valgt)',
                'attr' => array('placeholder' => 'Svar på undersøkelse!'),
                'required' => false,
            ])



        ->add('finishPageContent', CKEditorType::class, [
            'label' => 'Melding på sluttside (viser "Takk for svaret!" om tom)',

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

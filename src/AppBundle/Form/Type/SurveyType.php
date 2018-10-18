<?php

namespace AppBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SurveyType extends AbstractType
{
    private $isAdminSurvey = false;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->isAdminSurvey = $options['isAdminSurvey'];

        $builder->add('semester', EntityType::class, array(
            'label' => 'Semester',
            'class' => 'AppBundle:Semester',
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('s')
                    ->where('s.admissionEndDate > :limit')
                    ->setParameter('limit', new \DateTime('now -1 year'))
                    ->orderBy('s.semesterStartDate', 'DESC');
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
            ]);

        if ($this->isAdminSurvey()) {
            $builder->add('team_survey', ChoiceType::class, [
                    'label' => 'Dette er en undersøkelse rettet mot teammedlem (popup)',
                    'multiple' => false,
                    'expanded' => true,
                    'choices' => [
                        'Ja' => true,
                        'Nei' => false,
                    ]

                ])

                    ->add('surveyPopUpMessage', CKEditorType::class, [
                        'label' => 'Pop-up melding, vises kun hvis ja er valgt.',
                    ]);
        }

        $builder->add('confidential', ChoiceType::class, array(
                'label' => 'Resultater kan leses av',
                'multiple' => false,
                'expanded' => true,
                'choices' => [
                    'Medlemmer og Ledere' => false,
                    'Kun Ledere' => true
                ]
            ))






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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Survey',
            'isAdminSurvey' => false,
        ));
    }


    public function setAdminSurvey(bool $isSuperAdmin)
    {
        $this->isAdminSurvey = $isSuperAdmin;
    }

    public function isAdminSurvey() : bool
    {
        return $this->isAdminSurvey;
    }


    public function getBlockPrefix()
    {
        return 'survey';
    }
}

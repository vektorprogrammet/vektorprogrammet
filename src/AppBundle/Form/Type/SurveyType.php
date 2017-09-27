<?php

namespace AppBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SurveyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('semester', 'entity', array(
            'label' => 'Semester',
            'class' => 'AppBundle:Semester',
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('s')
                    ->where('s.admissionEndDate > :limit')
                    ->setParameter('limit', new \DateTime('now -1 year'))
                    ->orderBy('s.semesterStartDate', 'DESC');
            },
        ))

        ->add('name', 'text', array(
            'label' => false,
            'attr' => array('placeholder' => 'Fyll inn tittel til undersøkelse'),
        ))
            
            ->add('showCustomFinishPage', ChoiceType::class, [
                'label' => 'Sluttside',
                'multiple' => false,
                'expanded' => true,
                'choices' => [
                    false => 'Vis standard sluttside',
                    true => 'Vis tilpasset sluttside',
                ]
            ])
            
            ->add('finishPageContent', CKEditorType::class, [
                'label' => 'Tilpasset sluttside',
            ])

        ->add('surveyQuestions', 'collection', array(
            'type' => new SurveyQuestionType(),
            'allow_add' => true,
            'allow_delete' => true,
            'prototype_name' => '__q_prot__',
        ))

        ->add('save', 'submit', array(
            'label' => 'Lagre',
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Survey',
        ));
    }

    public function getName()
    {
        return 'survey';
    }
}

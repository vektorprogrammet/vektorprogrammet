<?php

namespace AppBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class SurveyPupilType extends AbstractType {


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\SurveyPupil',
        ));
    }

    public function getName(){
        return 'Survey'; // This must be unique
    }

    public function buildForm(FormBuilderInterface $builder, array $options){

        $builder
            ->add('school', 'entity', array(
                'placeholder' => 'Skole',
                'label' => 'Skole',
                'class' => 'AppBundle:School',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.name', 'ASC');
                }
            ))
            ->add('question1', 'choice', array(
                'label' => '1. Kjønn',
                'choices' => array(
                    '1' => 'Gutt',
                    '2' => 'Jente'
                ),
                'expanded' => true,
                'multiple' => false,
                'required' => true
            ))

            ->add('question2', 'choice', array(
                'label' => '2. Klassetrinn',
                'choices' => array(
                    '1' => '5. Klasse',
                    '2' => '6. Klasse',
                    '3' => '7. Klasse',
                    '4' => '8. Klasse',
                    '5' => '9. Klasse',
                    '6' => '10. Klasse',
                ),
                'expanded' => true,
                'multiple' => false,
                'required' => true
            ))

            ->add('question3', 'choice', array(
                'label' => '3. Jeg var tilstede da vektorassistentene kom på besøk for å hjelpe',
                'choices' => array(
                    '1' => 'Ja',
                    '2' => 'Nei'
                ),
                'expanded' => true,
                'multiple' => false,
                'required' => true
            ))
            ->add('question4', 'choice', array(
                'label' => '4. Jeg har fått hjelp av vektorassistentene',
                'choices' => array(
                    '1' => 'Ja',
                    '2' => 'Nei'
                ),
                'expanded' => true,
                'multiple' => false,
                'required' => true
            ))
            ->add('question5', 'choice', array(
                'label' => '5. Jeg syntes det gikk greit å spørre vektorassistentene om hjelp',
                'choices' => array(
                    '1' => 'Helt enig',
                    '2' => 'Noe enig',
                    '3' => 'Nøytral',
                    '4' => 'Noe uenig',
                    '5' => 'Helt uenig'
                ),
                'expanded' => true,
                'multiple' => false,
                'required' => true
            ))
            ->add('question6', 'choice', array(
                'label' => '6. Jeg fikk lettere hjelp da vektorassistentene var i timen',
                'choices' => array(
                    '1' => 'Helt enig',
                    '2' => 'Noe enig',
                    '3' => 'Nøytral',
                    '4' => 'Noe uenig',
                    '5' => 'Helt uenig'
                ),
                'expanded' => true,
                'multiple' => false,
                'required' => true
            ))
            ->add('question7', 'choice', array(
                'label' => '7. Vektorassistentene kunne pensum og forklarte slik at jeg fikk mer forståelse for oppgaven',
                'choices' => array(
                    '1' => 'Helt enig',
                    '2' => 'Noe enig',
                    '3' => 'Nøytral',
                    '4' => 'Noe uenig',
                    '5' => 'Helt uenig'
                ),
                'expanded' => true,
                'multiple' => false,
                'required' => true
            ))
            ->add('question8', 'choice', array(
                'label' => '8. Jeg synes matte er mer spennende etter møtet med vektorassistentene',
                'choices' => array(
                    '1' => 'Helt enig',
                    '2' => 'Noe enig',
                    '3' => 'Nøytral',
                    '4' => 'Noe uenig',
                    '5' => 'Helt uenig'
                ),
                'expanded' => true,
                'multiple' => false,
                'required' => true
            ))
            ->add('question9', 'choice', array(
                'label' => '9. Jeg vil at vektorassistentene kommer tilbake',
                'choices' => array(
                    '1' => 'Helt enig',
                    '2' => 'Noe enig',
                    '3' => 'Nøytral',
                    '4' => 'Noe uenig',
                    '5' => 'Helt uenig'
                ),
                'expanded' => true,
                'multiple' => false,
                'required' => true
            ))
            ->add('save', 'submit', array(
                'label' => 'Send inn'));

    }

}
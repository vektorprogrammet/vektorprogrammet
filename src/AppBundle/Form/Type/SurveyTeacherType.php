<?php

namespace AppBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class SurveyTeacherType extends AbstractType {


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\SurveyTeacher',
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
                'label' => '1. Klassetrinn',
                'choices' => array(
                    '1' => '8. Klasse',
                    '2' => '9. Klasse',
                    '3' => '10. Klasse'
                ),
                'expanded' => true,
                'multiple' => false,
                'required' => true
            ))
            ->add('question2', 'choice', array(
                'label' => '2. Det var nyttig å ha vektorassistentene i klassen.',
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
            ->add('question3', 'choice', array(
                'label' => '3. Vektorassistentene var kvalifiserte for jobben.',
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
            ->add('question4', 'choice', array(
                'label' => '4. Det var for mange studentassistenter tilstede.',
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
            ->add('question5', 'choice', array(
                'label' => '5. Det var god kontakt og informasjonsflyt på forhånd.',
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
                'label' => '6. Jeg ønsker at prosjektet fortsetter.',
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
                'label' => '7. Jeg tror elevene har blitt mer motivert for matematikk som følge av prosjektet.',
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
                'label' => '8. Arbeidsbelastningen ble mindre når vektorassistentene var på skolen.',
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
                'label' => '9. Undervisning ble bedre tilpasset for elevene.',
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
            ->add('question10', 'textarea', array(
                'label' => '10. Har du noen kommentarer om vektorprogrammet som vi kan bruke videre? (Må ikke fylles ut)',
                'attr' => array('cols' => 5, 'rows' => '7'),
                'required' => false
            ))
            ->add('save', 'submit', array(
                'label' => 'Send inn'));

    }

}
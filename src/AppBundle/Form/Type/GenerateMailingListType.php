<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class GenerateMailingListType extends AbstractType
{
    private $semesters;

    public function __construct($semesters)
    {
        $this->semesters = $semesters;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('semester', 'entity', array(
                'class' => 'AppBundle:Semester',
                'label' => 'Velg semester',
                'choices' => $this->semesters,
                'required' => true,
            ))
            ->add('type', 'choice', array(
                'label' => 'Velg type',
                'choices' => array(
                    'Assistent' => 'Assistent',
                    'Team' => 'Team',
                    'Alle' => 'Alle',
                ),
                'required' => true,
            ))
            ->add('save', 'submit', array(
                'label' => 'Generer',
            ));
    }
}

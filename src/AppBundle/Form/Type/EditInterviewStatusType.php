<?php

namespace AppBundle\Form\Type;

use AppBundle\Type\InterviewStatusType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class EditInterviewStatus extends AbstractType
{
    protected $roles;

    protected $department;

    public function __construct()
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('changeInterviewStatus', ChoiceType::class, array(
            'choices' => array(
                'Ingen svar' => InterviewStatusType::PENDING,
                'Akseptert' => InterviewStatusType::ACCEPTED,
                'Ny tid ønskes' => InterviewStatusType::REQUEST_NEW_TIME,
                'Kansellert' => InterviewStatusType::CANCELLED,
            ),
        ));

        $builder->add('save', 'submit', array(
            'label' => 'Lagre',
        ));
    }

    public function getName()
    {
        return 'application';
    }
}

<?php

namespace AppBundle\Form\Type;

use AppBundle\Type\InterviewStatusType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class EditInterviewStatusType extends AbstractType
{
    protected $roles;

    protected $department;

    public function __construct()
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('changeInterviewStatus', ChoiceType::class, array(
            'label' => 'Status',
            'choices' => array(
                InterviewStatusType::PENDING => 'Ingen svar',
                InterviewStatusType::ACCEPTED => 'Akseptert',
                InterviewStatusType::REQUEST_NEW_TIME => 'Ny tid ønskes',
                InterviewStatusType::CANCELLED => 'Kansellert',
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

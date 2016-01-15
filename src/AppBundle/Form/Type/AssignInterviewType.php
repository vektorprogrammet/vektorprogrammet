<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AssignInterviewType extends AbstractType
{
    protected $roles;

    protected $department;

    public function __construct($roles, $department = null) {
        $this->roles = $roles;
        $this->department = $department;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('interview', new CreateInterviewType($this->roles, $this->department));

        $builder->add('save', 'submit', array(
            'label' => 'Lagre',
        ));
    }

    public function getName()
    {
        return 'user';
    }
}
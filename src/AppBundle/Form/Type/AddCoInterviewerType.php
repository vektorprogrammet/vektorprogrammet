<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;


class AddCoInterviewerType extends AbstractType
{
    private $teamUsers;

    public function __construct($teamUsers)
    {
        $this->teamUsers = $teamUsers;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', EntityType::class, array (
                'class' => User::class,
                'label' => 'Medintervjuer',
                'choices' => $this->teamUsers,
                'by_reference' => false,
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Legg til'
            ));
    }
}

<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddCoInterviewerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', EntityType::class, array(
                'class' => User::class,
                'label' => 'Medintervjuer',
                'choices' => $options['teamUsers'],
                'by_reference' => false,
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Legg til'
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'teamUsers' => []
        ]);
    }
}

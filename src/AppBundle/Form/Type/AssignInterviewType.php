<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssignInterviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('interview', CreateInterviewType::class, [
            'roles' => $options['roles'],
            'department' => $options['department'],
        ]);

        $builder->add('save', SubmitType::class, array(
            'label' => 'Lagre',
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'roles' => [],
            'department' => null
        ]);
    }


    public function getBlockPrefix()
    {
        return 'application';
    }
}

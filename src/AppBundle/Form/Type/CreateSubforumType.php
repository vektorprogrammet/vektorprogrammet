<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CreateSubforumType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'Navn',
            ))
           ->add('schools', 'entity', array(
                'required' => false,
                'class' => 'AppBundle:School',
                'multiple' => true,
                'expanded' => true,
                'label' => 'Velg hvilken skoler som skal ha tilgang til subforumet.',
            ))
            ->add('teams', 'entity', array(
                'required' => false,
                'class' => 'AppBundle:Team',
                'multiple' => true,
                'expanded' => true,
                'label' => 'Velg team som tilhører forumet.',
            ))
            ->add('type', 'choice', array(
                'required' => true,
                'choices' => array(
                    'team' => 'Team',
                    'school' => 'Skole',
                    'general' => 'Generelt',
                ),
            ))
            ->add('schoolDocument', 'ckeditor', array(
                'label' => 'Skoledokument (valgfritt)',
                'required' => false,
            ))
            ->add('Lagre', 'submit', array(

            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Subforum',
        ));
    }

    public function getName()
    {
        return 'createSubforum';
    }
}

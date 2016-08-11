<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CreateForumType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'Navn',
            ))
            ->add('description', 'text', array(
                'label' => 'Beskrivelse',
            ))
            ->add('type', 'choice', array(
                'required' => true,
                'choices' => array(
                    'team' => 'Team',
                    'school' => 'Skole',
                    'general' => 'Generelt',
                ),
            ))
            ->add('Lagre', 'submit', array(

            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Forum',
        ));
    }

    public function getName()
    {
        return 'createForum';
    }
}

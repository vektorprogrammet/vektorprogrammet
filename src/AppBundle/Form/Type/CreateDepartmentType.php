<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CreateDepartmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'Navn',
            ))
            ->add('shortName', 'text', array(
                'label' => 'Forkortet navn',
            ))
            ->add('email', 'text', array(
                'label' => 'E-post',
            ))
            ->add('address', 'text', array(
                'label' => 'Adresse:',
                'required' => false
            ))
            ->add('city', 'text', array(
                'label' => 'By',
            ))
            ->add('latitude', 'text', array(
                'label' => 'Latitude',
                'required' => false
            ))
            ->add('longitude', 'text', array(
                'label' => 'Longitude',
                'required' => false
            ))
            ->add('slackChannel', 'text', array(
                'label' => 'Privat Slack Channel',
                'required' => false,
                'attr' => ['placeholder' => 'eks. #styret_REGION']
            ))
            ->add('active', CheckboxType::class, array(
                'label' => 'Aktiv?',
                'required' => false
            ))
            ->add('save', 'submit', array(
                'label' => 'Opprett',
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Department',
        ));
    }

    public function getName()
    {
        return 'createDepartment';
    }
}

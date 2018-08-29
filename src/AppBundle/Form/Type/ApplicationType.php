<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // The fields that populate the form
        $builder
            ->add('user', CreateUserOnApplicationType::class, array(
                'label' => '',
                'departmentId' => $options['departmentId']
            ))
        ->add('yearOfStudy', ChoiceType::class, [
            'label' => 'Årstrinn',
            'choices' => array(
                '1. klasse' => '1. klasse',
                '2. klasse' => '2. klasse',
                '3. klasse' => '3. klasse',
                '4. klasse' => '4. klasse',
                '5. klasse' => '5. klasse',
            ),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Application',
            'user' => null,
            'allow_extra_fields' => true,
            'departmentId' => null,
            'environment' => 'prod',
        ));
    }

    public function getBlockPrefix()
    {
        return 'application'; // This must be unique
    }
}

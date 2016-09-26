<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamApplicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name', 'file', array(
                'label' => 'Navn',
            ))
            ->add('email', 'file', array(
                'label' => 'Email',
            ))
            ->add('applicationText', 'text', array(
                'label' => 'Søknadstekst',
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\TeamApplication',
        ));
    }

    public function getName()
    {
        return 'app_bundle_team_application_type';
    }
}

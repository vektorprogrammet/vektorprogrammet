<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamApplicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name', 'text', array(
                'label' => 'Navn',
            ))
            ->add('email', 'email', array(
                'label' => 'Email',
            ))
            ->add('applicationText', 'textarea', array(
                'label' => 'Søknadstekst',
                'attr' => array('rows' => 10),
            ))
            ;
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

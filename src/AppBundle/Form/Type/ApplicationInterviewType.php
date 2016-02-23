<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ApplicationInterviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('applicationPractical', new ApplicationPracticalType(), array(
            'data_class' => 'AppBundle\Entity\Application'
        ));

        $builder->add('heardAboutFrom', 'choice', array(
            'label' => 'Hvor hørte du om Vektorprogrammet?',
            'choices' => array(
                'Blesting' => 'Blesting',
                'Stand' => 'Stand',
                'Infomail/nettsida/facebook etc' => 'Infomail/nettsida/facebook etc',
                'Bekjente' => 'Bekjente',
                'Bekjente i styret' => 'Bekjente i styret',
                'Plakater/flyers' => 'Plakater/Flyers',
                'Linjeforeningen (f.eks fadderukene)' => 'Linjeforeningen (f.eks fadderukene)'
            ),
            'expanded' => true,
            'multiple' => true
        ));

        $builder->add('interview', new InterviewType());

        $builder->add('save', 'submit', array(
            'label' => 'Lagre',
        ));

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Application',
        ));
    }

    public function getName()
    {
        return 'application';
    }
}
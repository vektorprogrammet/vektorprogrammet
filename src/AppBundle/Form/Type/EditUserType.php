<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EditUserType extends AbstractType
{
    private $department;
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->department = $options['department'];
        
        $builder
            ->add('user_name', 'text', array(
                'label' => 'Brukernavn',
            ))
            ->add('firstName', 'text', array(
                'label' => 'Fornavn',
            ))
            ->add('lastName', 'text', array(
                'label' => 'Etternavn',
            ))
            ->add('email', 'text', array(
                'label' => 'E-post',
            ))
            ->add('phone', 'text', array(
                'label' => 'Telefon',
            ))
            ->add('fieldOfStudy', 'entity', array(
                'label' => 'Linje',
                'class' => 'AppBundle:FieldOfStudy',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('f')
                        ->orderBy('f.shortName', 'ASC')
                        ->where('f.department = ?1')
                        // Set the parameter to the department ID that the current user belongs to.
                        ->setParameter(1, $this->department);
                },
            ))
            ->add('save', 'submit', array(
                'label' => 'Lagre',
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
            'department' => 'AppBundle\Entity\Department'
        ));
    }

    public function getName()
    {
        return 'editUser';
    }
}

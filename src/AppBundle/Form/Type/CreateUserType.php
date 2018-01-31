<?php

namespace AppBundle\Form\Type;

use AppBundle\Role\Roles;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateUserType extends AbstractType
{
    private $department;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->department = $options['department'];

        $builder
            ->add('firstName', 'text', array(
                'label' => 'Fornavn',
            ))
            ->add('lastName', 'text', array(
                'label' => 'Etternavn',
            ))
            ->add('gender', 'choice', array(
                'label' => 'Kjønn',
                'choices' => array(
                    0 => 'Mann',
                    1 => 'Dame',
                ),
            ))
            ->add('phone', 'text', array(
                'label' => 'Telefon',
            ))
            ->add('email', 'text', array(
                'label' => 'E-post',
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
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
            'department' => 'AppBundle\Entity\Department'
        ));
    }

    public function getName()
    {
        return 'createUser';
    }
}

<?php

namespace AppBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CreateUserOnApplicationType extends AbstractType
{
    private $departmentId;
    public function __construct($departmentId)
    {
        $this->departmentId = $departmentId;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', 'text', array(
                'label' => 'Fornavn',
            ))
            ->add('lastName', 'text', array(
                'label' => 'Etternavn',
            ))
            ->add('phone', 'text', array(
                'label' => 'Telefon',
            ))
            ->add('email', 'email', array(
                'label' => 'E-post',
            ))
            ->add('gender', ChoiceType::class, array(
                'choices' => [
                    0 => 'Mann',
                   1 => 'Dame'
                ],
                'label' => 'Kjønn'
            ))
            ->add('fieldOfStudy', 'entity', array(
                'label' => 'Linje',
                'class' => 'AppBundle:FieldOfStudy',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('f')
                        ->orderBy('f.shortName', 'ASC')
                        ->where('f.department = ?1')
                        // Set the parameter to the department ID that the current user belongs to.
                        ->setParameter(1, $this->departmentId);
                },
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
        ));
    }

    public function getName()
    {
        return 'createUser';
    }
}

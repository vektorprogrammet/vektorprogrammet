<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Repository\FieldOfStudyRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UserDataForSubstituteType extends AbstractType
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
            ->add('phone', 'text', array(
                'label' => 'Tlf',
            ))
            ->add('email', 'text', array(
                'label' => 'E-post',
            ))
            ->add('fieldOfStudy', 'entity', array(
                'label' => 'Linje',
                'class' => 'AppBundle:FieldOfStudy',

                //'query_builder' => Controller?->getDoctrine()->getRepository('AppBundle:FieldOfStudy')->findByDepartment($this->department);
                // Hvordan får jeg kjørt funksjoner på Controller her?

                'query_builder' => function (FieldOfStudyRepository $er) {
                    return $er->createQueryBuilder('f')
                        ->orderBy('f.shortName', 'ASC')
                        ->where('f.department = ?1')
                        // Set the parameter to the department ID that the current user belongs to.
                        ->setParameter(1, $this->department);
                },
            ));


        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
            'department' => null,
        ));
    }

    public function getName()
    {
        return 'userDataForSubstitute';
    }
}

<?php

namespace AppBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ->add('firstName', TextType::class, array(
                'label' => 'Fornavn',
                'attr' => array('autocomplete' => 'given-name')
            ))
            ->add('lastName', TextType::class, array(
                'label' => 'Etternavn',
                'attr' => array('autocomplete' => 'family-name')
            ))
            ->add('phone', TelType::class, array(
                'label' => 'Telefon',
                'attr' => array('autocomplete' => 'tel')
            ))
            ->add('email', EmailType::class, array(
                'label' => 'E-post',
                'attr' => array('autocomplete' => 'email')
            ))
            ->add('gender', ChoiceType::class, array(
                'choices' => [
                    0 => 'Mann',
                   1 => 'Dame'
                ],
                'label' => 'Kjønn'
            ))
            ->add('fieldOfStudy', EntityType::class, array(
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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
        ));
    }

    public function getBlockPrefix()
    {
        return 'createUser';
    }
}

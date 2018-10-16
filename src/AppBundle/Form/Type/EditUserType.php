<?php

namespace AppBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditUserType extends AbstractType
{
    private $department;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->department = $options['department'];

        $builder
            ->add('user_name', TextType::class, array(
                'label' => 'Brukernavn',
            ))
            ->add('firstName', TextType::class, array(
                'label' => 'Fornavn',
            ))
            ->add('lastName', TextType::class, array(
                'label' => 'Etternavn',
            ))
            ->add('email', TextType::class, array(
                'label' => 'E-post',
            ))
            ->add('phone', TextType::class, array(
                'label' => 'Telefon',
            ))
            ->add('fieldOfStudy', EntityType::class, array(
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
            ->add('accountNumber', TextType::class, array(
                'label' => 'Kontonummer',
                'required' => false,
                'attr' => array('oninput' => 'validateBankAccountNumber(this)'),
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
            'department' => 'AppBundle\Entity\Department',
        ));
    }

    public function getBlockPrefix()
    {
        return 'editUser';
    }
}

<?php

namespace App\Admission\Form;

use App\Support\Form\TelType;
use App\Organization\Infrastructure\Entity\FieldOfStudy;
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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Fornavn',
                'attr' => ['autocomplete' => 'given-name'],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Etternavn',
                'attr' => ['autocomplete' => 'family-name'],
            ])
            ->add('phone', TelType::class, [
                'label' => 'Telefon',
                'attr' => ['autocomplete' => 'tel'],
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-post',
                'attr' => ['autocomplete' => 'email'],
            ])
            ->add('gender', ChoiceType::class, [
                'choices' => [
                    'Mann' => 0,
                    'Dame' => 1,
                ],
                'label' => 'Kjønn',
            ])
            ->add('fieldOfStudy', EntityType::class, [
                'label' => 'Linje',
                'class' => FieldOfStudy::class,
                'query_builder' => fn (EntityRepository $er) => $er->createQueryBuilder('f')
                    ->orderBy('f.shortName', 'ASC')
                    ->where('f.department = ?1')
                    // Set the parameter to the department ID that the current user belongs to.
                    ->setParameter(1, $options['departmentId']),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => \App\Entity\User::class,
            'departmentId' => null,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'createUser';
    }
}

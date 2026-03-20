<?php

namespace App\Identity\Form;

use App\Organization\Infrastructure\Entity\FieldOfStudy;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditUserType extends AbstractType
{
    private $department;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->department = $options['department'];

        $builder
            ->add('user_name', TextType::class, [
                'label' => 'Brukernavn',
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Fornavn',
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Etternavn',
            ])
            ->add('email', TextType::class, [
                'label' => 'E-post',
            ])
            ->add('phone', TextType::class, [
                'label' => 'Telefon',
            ])
            ->add('fieldOfStudy', EntityType::class, [
                'label' => 'Linje',
                'class' => FieldOfStudy::class,
                'query_builder' => fn (EntityRepository $er) => $er->createQueryBuilder('f')
                    ->orderBy('f.shortName', 'ASC')
                    ->where('f.department = ?1')
                    // Set the parameter to the department ID that the current user belongs to.
                    ->setParameter(1, $this->department),
            ])
            ->add('accountNumber', TextType::class, [
                'label' => 'Kontonummer',
                'required' => false,
                'attr' => ['oninput' => 'validateBankAccountNumber(this)'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => \App\Identity\Infrastructure\Entity\User::class,
            'department' => \App\Organization\Infrastructure\Entity\Department::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'editUser';
    }
}

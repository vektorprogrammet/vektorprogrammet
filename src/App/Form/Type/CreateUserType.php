<?php

namespace App\Form\Type;

use App\Organization\Infrastructure\Entity\FieldOfStudy;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateUserType extends AbstractType
{
    private $department;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->department = $options['department'];

        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Fornavn',
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Etternavn',
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'Kjønn',
                'choices' => [
                    'Mann' => 0,
                    'Dame' => 1,
                ],
            ])
            ->add('phone', TextType::class, [
                'label' => 'Telefon',
            ])
            ->add('email', TextType::class, [
                'label' => 'E-post',
            ])
            ->add('fieldOfStudy', EntityType::class, [
                'label' => 'Linje',
                'class' => FieldOfStudy::class,
                'query_builder' => fn (EntityRepository $er) => $er->createQueryBuilder('f')
                    ->orderBy('f.shortName', 'ASC')
                    ->where('f.department = ?1')
                    // Set the parameter to the department ID that the current user belongs to.
                    ->setParameter(1, $this->department),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => \App\Entity\User::class,
            'department' => \App\Organization\Infrastructure\Entity\Department::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'createUser';
    }
}

<?php

namespace AppBundle\Form\Type;

use AppBundle\Service\RoleManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateUserType extends AbstractType
{
    private $department;
    private $userRole;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->department = $options['department'];
        $this->userRole = $options['user_role'];

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

        if ($this->userRole === RoleManager::ROLE_TEAM_LEADER) {
            $builder->add('role', 'choice', array(
                'label' => 'Rolle',
                'mapped' => false,
                'choices' => array(
                    RoleManager::ROLE_ALIAS_ASSISTANT => 'Assistent',
                    RoleManager::ROLE_ALIAS_TEAM_MEMBER => 'Teammedlem',
                    RoleManager::ROLE_ALIAS_TEAM_LEADER => 'Teamleder',
                ),
            ));
        } elseif ($this->userRole === RoleManager::ROLE_TEAM_MEMBER) {
            $builder->add('role', 'choice', array(
                'label' => 'Rolle',
                'mapped' => false,
                'choices' => array(
                    RoleManager::ROLE_ALIAS_ASSISTANT => 'Assistent',
                ),
            ));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
            'department' => 'AppBundle\Entity\Department',
            'user_role' => RoleManager::ROLE_TEAM_MEMBER,
        ));
    }

    public function getName()
    {
        return 'createUser';
    }
}

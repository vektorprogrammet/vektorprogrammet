<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\AccessRule;
use AppBundle\Entity\Role;
use AppBundle\Entity\Team;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccessRuleType extends AbstractType
{
    private $roles;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->roles = $options['roles'];

        $builder
            ->add("name", TextType::class)
            ->add("resource", TextType::class)
            ->add("roles", EntityType::class, [
                'label' => false,
                "expanded" => true,
                "multiple" => true,
                "class" => Role::class,
            ])
            ->add("teams", EntityType::class, [
                'label' => false,
                "expanded" => true,
                "multiple" => true,
                "class" => Team::class,
                "group_by" => "department.city"

            ])
            ->add('forExecutiveBoard', CheckboxType::class, [
                'label' => 'Hovedstyret'
            ])
            ->add("users", EntityType::class, [
                'label' => false,
                "expanded" => true,
                "multiple" => true,
                "class" => User::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->select('u')
                        ->join('u.roles', 'r')
                        ->where('r.role IN (:roles)')
                        ->orderBy('u.firstName')
                        ->setParameter('roles', $this->roles);
                },
                "group_by" => "fieldOfStudy.department.city"
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => AccessRule::class,
            'roles' => []
        ]);
    }

    public function getName()
    {
        return 'app_bundle_access_rule_type';
    }
}

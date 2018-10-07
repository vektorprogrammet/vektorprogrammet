<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\AccessRule;
use AppBundle\Entity\Role;
use AppBundle\Entity\Team;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Route;

class AccessRuleType extends AbstractType
{
    private $roles;
    private $routes;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->roles = $options['roles'];
        $this->routes = $options['routes'];

        $choices = [];
        /**
         * @var Route $routeObj
         */
        foreach ($this->routes as $route => $routeObj) {
            $choices[$route] = $routeObj->getPath();
        }

        $builder
            ->add("name", TextType::class)
            ->add("route", ChoiceType::class, [
                "expanded" => false,
                "multiple" => false,
                "choices" => $choices
            ])
            ->add("method", ChoiceType::class, [
                "expanded" => true,
                "multiple" => false,
                "choices" => [
                    "GET" => "GET",
                    "POST" => "POST",
                    "PUT" => "PUT",
                    "DELETE" => "DELETE"
                ]
            ])
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

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => AccessRule::class,
            "routes" => [],
            'roles' => []
        ]);
    }

    public function getName()
    {
        return 'app_bundle_access_rule_type';
    }
}

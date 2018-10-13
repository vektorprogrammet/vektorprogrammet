<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\AccessRule;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Route;

class RoutingAccessRuleType extends AccessRuleType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	parent::buildForm($builder, $options);

        $choices = [];
        /**
         * @var Route $routeObj
         */
        foreach ($options['routes'] as $route => $routeObj) {
            $choices[$route] = $routeObj->getPath();
        }

        $builder
            ->add("resource", ChoiceType::class, [
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
            ]);
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
        return 'app_bundle_routing_access_rule_type';
    }
}

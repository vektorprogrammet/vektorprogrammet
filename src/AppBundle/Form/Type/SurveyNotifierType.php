<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Survey;
use AppBundle\Entity\SurveyNotifier;
use AppBundle\Entity\UserGroup;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SurveyNotifierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("name", TextType::class, [
                'label' => 'Navn på varsel',
            ])

            ->add("timeOfNotification", DateTimeType::class, [
                'label' => "Varsel skal sendes fra",
            ])

            ->add("usergroup", EntityType::class, [
                'label' => false,
                "expanded" => true,
                "multiple" => false,
                "class" => UserGroup::class,
                "group_by" => "userGroupCollection",

            ])

            ->add("isEmail", ChoiceType::class, [
                'label' => "Skal varsel sendes på e-post?",
                "multiple" => false,
                "choices" => array(
                    "Ja" => true,
                    "Nei" => false,
                ),
            ])

            ->add("isSMS", ChoiceType::class, [
                'label' => "Skal varsel sendes på e-post?",
                "multiple" => false,
                "choices" => array(
                    "Ja" => true,
                    "Nei" => false,
                ),
            ])


            ->add("survey", EntityType::class, [
                'label' => "Varsel skal sendes om følgende undersøkelse",
                "expanded" => false,
                "multiple" => false,
                "class" => Survey::class,
                "group_by" => "semester",
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => SurveyNotifier::class,
        ]);
    }

    public function getName()
    {
        return 'app_bundle_survey_notifier_type';
    }
}

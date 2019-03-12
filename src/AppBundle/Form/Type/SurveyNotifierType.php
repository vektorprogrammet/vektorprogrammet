<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Repository\UserRepository;
use AppBundle\Entity\Survey;
use AppBundle\Entity\SurveyNotifier;
use AppBundle\Entity\User;
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
    private $canEdit;
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->canEdit = $options['canEdit'];
        $builder
            ->add("name", TextType::class, [
                'label' => 'Navn på varsel',
            ])

            ->add("timeOfNotification", DateTimeType::class, [
                'label' => "Varsel skal sendes fra (merk: vil bare tillate å sende fra gitt dato, vil ikke skje automatisk)",
                'disabled' => !$this->canEdit

            ])

            ->add("usergroup", EntityType::class, [
                'label' => false,
                "expanded" => true,
                "multiple" => false,
                "class" => UserGroup::class,
                "group_by" => "userGroupCollection",
                'disabled' => !$this->canEdit

            ])

            ->add("notificationType", ChoiceType::class, [
                'label' => "Varselstype",
                "multiple" => false,
                "expanded" => false,
                "choices" => array(
                    "E-post" => SurveyNotifier::$EMAIL_NOTIFICATION,
                    "SMS" => SurveyNotifier::$SMS_NOTIFICATION,
                ),
                'disabled' => !$this->canEdit
            ])



            ->add("survey", EntityType::class, [
                'label' => "Varsel skal sendes om følgende undersøkelse",
                "expanded" => false,
                "multiple" => false,
                "class" => Survey::class,
                "group_by" => "semester",
                'disabled' => !$this->canEdit
            ])


            ->add("senderUser", EntityType::class, [
                'label' => "Avsender",
                "expanded" => false,
                "multiple" => false,
                "class" => User::class,
                'query_builder' => function (UserRepository $ur) {
                    return $ur->findTeamMembers();
                },


        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => SurveyNotifier::class,
            "canEdit" => true,

        ]);
    }

    public function getName()
    {
        return 'app_bundle_survey_notifier_type';
    }
}

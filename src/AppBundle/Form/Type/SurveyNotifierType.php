<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Survey;
use AppBundle\Entity\SurveyNotificationCollection;
use AppBundle\Entity\UserGroup;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
                'disabled' => !$this->canEdit

            ])

            ->add("timeOfNotification", DateTimeType::class, [
                'label' => "Varsel skal sendes fra (merk: vil bare tillate å sende fra gitt dato, vil ikke skje automatisk)",
                'format' => 'dd.MM.yyyy HH:mm',
                'widget' => 'single_text',
                'disabled' => !$this->canEdit
            ])

            ->add("usergroups", EntityType::class, [
                'label' => false,
                "expanded" => true,
                "multiple" => true,
                "class" => UserGroup::class,
                "group_by" => "userGroupCollection",
                'disabled' => !$this->canEdit

            ])

            ->add("notificationType", ChoiceType::class, [
                'label' => "Varselstype",
                "multiple" => false,
                "expanded" => false,
                "choices" => array(
                    "E-post" => SurveyNotificationCollection::$EMAIL_NOTIFICATION,
                    "SMS" => SurveyNotificationCollection::$SMS_NOTIFICATION,
                ),
                'disabled' => !$this->canEdit
            ])

            ->add("emailType", ChoiceType::class, [
                'label' => "Eposttype",
                "multiple" => false,
                "expanded" => false,
                "choices" => array(
                    "Selvskrevet" => 0,
                    "Generell assistenttakkebrev" => 1,
                    "Personlig assistenttakkebrev" => 2,

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

            ->add('smsMessage', TextareaType::class, array(
                'attr' => array('rows' => '5'),
                'label' => false,
                'disabled' => !$this->canEdit
            ))

            ->add('emailEndMessage', CKEditorType::class, array(
                'attr' => array('rows' => '5'),
                'label' => false,
                'disabled' => !$this->canEdit
            ))

            ->add('emailFromName', TextType::class, array(
                'label' => 'Navn på avsender. (E-post til avsender vil være evaluering.ntnu@vektorprogrammet.no)',
                'disabled' => !$this->canEdit

            ))


            ->add('emailSubject', TextType::class, array(
                'label' => 'Epostemne',
                'disabled' => !$this->canEdit

            ))

            ->add('emailMessage', CKEditorType::class, array(
                'label' => false,
                'disabled' => !$this->canEdit


            ))


            ->add('preview', SubmitType::class, array(
                'label' => 'Forhåndsvis epost'
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => SurveyNotificationCollection::class,
            "canEdit" => true,

        ]);
    }

    public function getName()
    {
        return 'app_bundle_survey_notifier_type';
    }
}

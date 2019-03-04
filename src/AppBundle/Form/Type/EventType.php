<?php
namespace AppBundle\Form\Type;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;


class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array(
                'label' => false,
                'attr' => array('placeholder' => 'Fyll inn tittel til event'),
            ))

            ->add('description', CKEditorType::class, array(
                'label' => "Beskrivelse av arragement",
            ))


            ->add('startTime', DateTimeType::class, array(
                'label' => "Starttid for arrangement",
            ))

            ## TODO: MULIHET FOR IKKE Å HA TID


            ->add('endTime', DateTimeType::class, array(
                'label' => "Slutttid for arrangement",
            ))


            ->add('save', SubmitType::class, array(
            'label' => 'Lagre',
            )
        );

    }




}

<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Repository\SemesterRepository;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Utils\SemesterUtil;

class ChangeLogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class, array(
                'required' => true,
                'label' => false,
                'attr' => array('placeholder' => 'Fyll inn tittel til endring'),
            ))

        ->add( 'description', TextAreaType::class, array(
            'attr' => array('placeholder' => "Beskriv endringen"),
            ))
        ->add('gitHubLink', UrlType::class, array(

            ))
        ->add('date',DateType::class, array(

            ))
        ->add('description', CKEditorType::class, array(
        'required' => false,
        'config' => array(
            'height' => 500,
            'filebrowserBrowseRoute' => 'elfinder',
            'filebrowserBrowseRouteParameters' => array('instance' => 'team_editor'), ),
        'label' => 'Lang beskrivelse (valgfritt)',
        'attr' => array('class' => 'hide'), // Graceful loading, hides the textarea that is replaced by ckeditor
            ))

        ->add('save', SubmitType::class, array(
            'label' => 'Lagre',
            ));

       }
    

    public function getBlockPrefix()
    {
        return 'survey';
    }
}

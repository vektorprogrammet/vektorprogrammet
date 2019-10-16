<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class QuicklinkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        $builder
            ->add("title", TextType::class, array(
                'label' => "Skriv in titellen til QickLinken",
                "attr" => array(
                    'placeholder' => "Google Drive"
                )

            ))

            ->add("url", TextType::class, array(
                'label' => "Url'en til Quicklinken",
                 "attr" => array(
                    'placeholder'=> "www.drive.google.com"
                )

            ))

            ->add('iconUrl', FileType::class, array(
                'required' =>  true,
                'error_bubbling' => true,
                'data_class' => null,
                'label' => 'Last opp ny logo',
            ));


    }
}



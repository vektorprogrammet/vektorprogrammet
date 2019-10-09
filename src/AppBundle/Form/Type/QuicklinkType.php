<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class QuicklinkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        $builder
            ->add("title", TextType::class, array(
                'label' => "Skriv in titellen til QickLinken"

            ))
            ->add("url", TextType::class, array(
                'label' => "Skriv in titellen til QickLinken"

            ));
    }
}



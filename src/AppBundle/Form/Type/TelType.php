<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class TelType extends AbstractType
{
    public function getParent()
    {
        return TextType::class;
    }
    
    public function getBlockPrefix()
    {
        return 'tel';
    }
}

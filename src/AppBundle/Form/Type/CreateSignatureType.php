<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CreateSignatureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextType::class, array(
                'label' => ' ',
                'max_length' => 250,
            ))
            ->add('signature_path', FileType::class, array(
                'required' => false,
                'data_class' => null,
                'label' => 'Signaturbilde',
            ));
    }
}

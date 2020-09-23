<?php


namespace AppBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;


class TestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('HSPhoto', FileType::class, array(
            'required' => false,
            'error_bubbling' => true,
            'data_class' => null,
            'label' => 'Last opp nytt hs_photo',
        ));
    }

}
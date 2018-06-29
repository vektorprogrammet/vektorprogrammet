<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SponsorType extends AbstractType
{
    protected $id;
    private $router;

    public function __construct($id, $router)
    {
        $this->id = $id;
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setAction($this->router->generate('sponsors_update', array('id' => $this->id)))
            ->setMethod('POST')
            ->add('name', TextType::class, array(
                'label' => 'Sponsornavn',
            ))
            ->add('url', TextType::class, array(
                'label' => 'Sponsors hjemmeside',
            ))
            ->add('size', ChoiceType::class, array(
                'required' => true,
                'label' => 'Størrelse',
                'choices' => array(
                    'small' => 'Liten',
                    'medium' => 'Medium',
                    'large' => 'Stor',
                ),
                'multiple' => false,
                'expanded' => true,
            ))
            ->add('logoImagePath', FileType::class, array(
                'required' => false,
                'data_class' => null,
                'label' => 'Last opp ny logo',
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Lagre',
            ))
            ->add('delete', SubmitType::class, array(
                'label' => 'Slett',
            ));
    }

    public function getBlockPrefix()
    {
        return 'sponsor';
    }
}

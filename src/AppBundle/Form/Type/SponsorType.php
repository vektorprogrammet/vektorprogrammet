<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
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
            ->setAction($this->router->generate('sponsors_update', array('id' => $this->id))) //todo: http://stackoverflow.com/questions/19816061/how-do-i-generate-a-url-when-not-in-a-controller
            ->setMethod('POST')
            ->add('name', 'text', array(
                'label' => 'Sponsornavn',
            ))
            ->add('url', 'text', array(
                'label' => 'Sponsors hjemmeside',
            ))
            ->add('size', 'choice', array(
                'required' => true,
                'label' => 'Størrelse',
                'choices' => array(
                    'small' => 'Liten',
                    'medium' => 'Medium',
                    'large' => 'Stor'
                ),
                'multiple' => false,
                'expanded' => true,
            ))
            ->add('logoImagePath', 'file', array(
                'required' => false,
                'data_class' => null,
                'label' => 'Last opp ny logo',
            ))
            ->add('save', 'submit', array(
                'label' => 'Lagre',
            ))
            ->add('delete', 'submit', array(
                'label' => 'Slett',
            ));
    }

    public function getName()
    {
        return 'sponsor';
    }
}

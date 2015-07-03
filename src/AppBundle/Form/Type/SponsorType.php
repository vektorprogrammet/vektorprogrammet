<?php
/**
 * Created by PhpStorm.
 * User: Tommy
 * Date: 04.05.2015
 * Time: 18:24
 */

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;


class SponsorType extends AbstractType {

    protected $id;
    private $router;

    function __construct($id, $router){
        $this->id = $id;
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options){

        $builder
            ->setAction($this->router->generate('sponsors_update', array('id' => $this->id))) //todo: http://stackoverflow.com/questions/19816061/how-do-i-generate-a-url-when-not-in-a-controller
            ->setMethod('POST')
            ->add('name', 'text', array('label'=>'Sponsornavn'))
            ->add('url', 'text', array('label'=>'Sponsors hjemmeside'))
            ->add('logoImagePath', 'file', array('required' => false, 'data_class' => null, 'label'=>'Last opp ny logo'))
            ->add('save', 'submit', array('label'=>'Lagre'))
            ->add('delete', 'submit', array('label'=>'Slett'));
    }

    public function getName(){
        return 'sponsor';
    }
}
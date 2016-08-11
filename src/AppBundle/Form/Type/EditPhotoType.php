<?php
/**
 * Created by PhpStorm.
 * User: Tommy
 * Date: 06.05.2015
 * Time: 17:30.
 */

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class EditPhotoType extends AbstractType
{
    protected $id;
    protected $router;

    public function __construct($id, $router)
    {
        $this->id = $id;
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setAction($this->router->generate('gallery_edit_photo', array('id' => $this->id)))
            ->setMethod('POST')
            ->add('name', 'text', array('label' => 'Tekst'))
            ->add('imagePath', 'file', array('required' => false, 'data_class' => null, 'label' => 'Last opp ny logo'))
            ->add('save', 'submit', array('label' => 'Lagre'))
            ->add('delete', 'submit', array('label' => 'Slett'));
    }

    public function getName()
    {
        return 'edit_photo';
    }
}

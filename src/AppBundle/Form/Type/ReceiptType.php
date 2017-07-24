<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Form\Type\AccountNumberType;

class ReceiptType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', 'textarea', array(
                'label' => 'Beskrivelse',
                'attr' => array('rows' => 3),
            ))
            ->add('sum', 'money', array(
                'label' => 'Sum',
                'currency' => 'NOK',
                'attr' => array('pattern' => '\d*[.,]?\d+$'),
            ))
            ->add('user', AccountNumberType::class, array(
                'label' => false,
            ))
            ->add('picturePath', 'file', array(
                'label' => 'Last opp kvittering',
                'required' => $options['required'],
                'data_class' => null,
                'attr' => array('class' => 'receipt-upload-hack'),
                'label_attr' => array('class' => 'button'),
            ));
    }
}

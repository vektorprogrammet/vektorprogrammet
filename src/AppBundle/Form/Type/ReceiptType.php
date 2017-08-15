<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AppBundle\Form\Type\AccountNumberType;

class ReceiptType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextareaType::class, array(
                'label' => 'Beskrivelse',
                'required' => true,
                'attr' => array('rows' => 3, 'placeholder' => 'Hva har du lagt ut penger for? Når?'),
            ))
            ->add('sum', MoneyType::class, array(
                'label' => 'Sum',
                'required' => true,
                'currency' => null,
                'attr' => array('pattern' => '\d*[.,]?\d+$'),
            ))
            ->add('user', AccountNumberType::class, array(
                'label' => false,
            ))
            ->add('picturePath', FileType::class, array(
                'label' => 'Last opp bilde av kvittering',
                'required' => $options['picture_required'],
                'data_class' => null,
                'attr' => array('class' => 'receipt-upload-hack', 'accept' => 'image/*'),
                'label_attr' => array('class' => 'button tiny'),
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'picture_required' => true,
        ));
    }
}

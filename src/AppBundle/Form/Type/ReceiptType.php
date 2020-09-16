<?php

namespace AppBundle\Form\Type;

use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Form\Type\AccountNumberType;

class ReceiptType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $currentYear = intval((new DateTime())->format('Y'));
        $builder
            ->add('description', TextareaType::class, array(
                'label' => 'Beskrivelse',
                'required' => true,
                'attr' => array('rows' => 3, 'placeholder' => 'Hva har du lagt ut penger for?'),
            ))
            ->add('sum', MoneyType::class, array(
                'label' => 'Sum',
                'required' => true,
                'currency' => null,
                'attr' => array('pattern' => '\d*[.,]?\d+$'),
            ))
            ->add('receiptDate', DateType::class, array(
                'label' => 'Utleggsdato',
                'required' => true,
                'years' => [$currentYear, $currentYear - 1],
                'format' => 'ddMMMyyyy'
            ))
            ->add('user', AccountNumberType::class, array(
                'label' => false,
            ))
            ->add('picturePath', FileType::class, array(
                'label' => 'Velg/endre bilde av kvitteringen: ',
                'required' => $options['picture_required'],
                'data_class' => null,
                'attr' => array('class' => 'receipt-upload-hack', 'accept' => 'image/*'),
                'label_attr' => array('class' => 'button tiny'),
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'picture_required' => true,
        ));
    }
}

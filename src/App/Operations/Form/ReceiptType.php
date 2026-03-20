<?php

namespace App\Operations\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReceiptType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $currentYear = intval((new \DateTime())->format('Y'));
        $builder
            ->add('description', TextareaType::class, [
                'label' => 'Beskrivelse',
                'required' => true,
                'attr' => ['rows' => 3, 'placeholder' => 'Hva har du lagt ut penger for?'],
            ])
            ->add('sum', MoneyType::class, [
                'label' => 'Sum du ønsker tilbakebetalt',
                'required' => true,
                'currency' => null,
                'attr' => ['pattern' => '\d*[.,]?\d+$'],
            ])
            ->add('receiptDate', DateType::class, [
                'label' => 'Utleggsdato',
                'required' => true,
                'years' => [$currentYear, $currentYear - 1],
                'format' => 'ddMMMyyyy',
            ])
            ->add('user', AccountNumberType::class, [
                'label' => false,
            ])
            ->add('picturePath', FileType::class, [
                'label' => 'Velg/endre bilde av kvitteringen: ',
                'required' => $options['picture_required'],
                'data_class' => null,
                'attr' => ['class' => 'receipt-upload-hack', 'accept' => 'image/*,application/pdf'],
                'label_attr' => ['class' => 'button tiny'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'picture_required' => true,
        ]);
    }
}

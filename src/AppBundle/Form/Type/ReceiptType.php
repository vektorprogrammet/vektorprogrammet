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
use Symfony\Component\Validator\Constraints\File;

class ReceiptType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentYear = intval((new DateTime())->format('Y'));
        $builder
            ->add('description', TextareaType::class, array(
                'label' => 'Beskrivelse',
                'required' => true,
                'attr' => array('rows' => 3, 'placeholder' => 'Hva har du lagt ut penger for?'),
            ))
            ->add('sum', MoneyType::class, array(
                'label' => 'Sum du ønsker tilbakebetalt',
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
                'label' => 'Velg/endre kvittering (maks 2 MiB): ',
                'required' => $options['picture_required'],
                'data_class' => null,
                'help' => 'Last opp et bilde eller en PDF.',
                'constraints' => [
                    new File([
                        'maxSize' => '2Mi',
                        'maxSizeMessage' => 'Filen er for stor. Maksimal filstørrelse er 2 MiB.',
                        'uploadIniSizeErrorMessage' => 'Filen er for stor. Maksimal filstørrelse er 2 MiB.',
                    ]),
                ],
                'attr' => array('class' => 'receipt-upload-hack', 'accept' => 'image/*,application/pdf'),
                'label_attr' => array('class' => 'button tiny'),
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'picture_required' => true,
        ));
    }
}

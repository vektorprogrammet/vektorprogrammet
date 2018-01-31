<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DaysType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('monday', CheckboxType::class, array(
            'label' => 'Mandag passer IKKE',
            'required' => false,
        ));

        $builder->add('tuesday', CheckboxType::class, array(
            'label' => 'Tirsdag passer IKKE',
            'required' => false,
        ));

        $builder->add('wednesday', CheckboxType::class, array(
            'label' => 'Onsdag passer IKKE',
            'required' => false,
        ));

        $builder->add('thursday', CheckboxType::class, array(
            'label' => 'Torsdag passer IKKE',
            'required' => false,
        ));

        $builder->add('friday', CheckboxType::class, array(
            'label' => 'Fredag passer IKKE',
            'required' => false,
        ));

        /* Invert the truth values */
        $builder->get('monday')
            ->addModelTransformer(new CallbackTransformer(
                function ($in) {
                    return !$in; /* The object value displayed */
                },
                function ($in) {
                    return !$in; /* The submitted value into the object */
                }
            ));


        $builder->get('tuesday')
            ->addModelTransformer(new CallbackTransformer(
                function ($in) {
                    return !$in;
                },
                function ($in) {
                    return !$in;
                }
            ));


        $builder->get('wednesday')
            ->addModelTransformer(new CallbackTransformer(
                function ($in) {
                    return !$in;
                },
                function ($in) {
                    return !$in;
                }
            ));


        $builder->get('thursday')
            ->addModelTransformer(new CallbackTransformer(
                function ($in) {
                    return !$in;
                },
                function ($in) {
                    return !$in;
                }
            ));

        $builder->get('friday')
            ->addModelTransformer(new CallbackTransformer(
                function ($in) {
                    return !$in;
                },
                function ($in) {
                    return !$in;
                }
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Application',
            'inherit_data' => true,
            'label' => '',
        ));
    }

    public function getName()
    {
        return 'application';
    }
}

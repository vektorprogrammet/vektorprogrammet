<?php

namespace App\Admission\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DaysType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('monday', CheckboxType::class, [
            'label' => 'Mandag passer IKKE',
            'required' => false,
        ]);

        $builder->add('tuesday', CheckboxType::class, [
            'label' => 'Tirsdag passer IKKE',
            'required' => false,
        ]);

        $builder->add('wednesday', CheckboxType::class, [
            'label' => 'Onsdag passer IKKE',
            'required' => false,
        ]);

        $builder->add('thursday', CheckboxType::class, [
            'label' => 'Torsdag passer IKKE',
            'required' => false,
        ]);

        $builder->add('friday', CheckboxType::class, [
            'label' => 'Fredag passer IKKE',
            'required' => false,
        ]);

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
                fn ($in) => !$in,
                fn ($in) => !$in
            ));

        $builder->get('wednesday')
            ->addModelTransformer(new CallbackTransformer(
                fn ($in) => !$in,
                fn ($in) => !$in
            ));

        $builder->get('thursday')
            ->addModelTransformer(new CallbackTransformer(
                fn ($in) => !$in,
                fn ($in) => !$in
            ));

        $builder->get('friday')
            ->addModelTransformer(new CallbackTransformer(
                fn ($in) => !$in,
                fn ($in) => !$in
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => \App\Admission\Infrastructure\Entity\Application::class,
            'inherit_data' => true,
            'label' => '',
        ]);
    }

    public function getBlockPrefix()
    {
        return 'application';
    }
}

<?php

namespace AppBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditAddressType extends AbstractType
{
    private $user;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->user = $options['user'];

        $builder
            ->add('address', TextType::class, array(
                'label' => 'Addresse',
            ))
            ->add('city', TextType::class, array(
                'label' => 'By',
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Address',
            'user' => 'AppBundle\Entity\User',
        ));
    }

    public function getBlockPrefix()
    {
        return 'editAddress';
    }
}

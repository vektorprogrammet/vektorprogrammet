<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CreateExecutiveBoardMemberType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', 'entity', array(
                'label' => 'Bruker',
                'class' => 'AppBundle:User',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->Join('u.fieldOfStudy', 'fos')
                        ->Join('fos.department', 'd')
                        ->addOrderBy('d.name')
                        ->addOrderBy('u.firstName', 'ASC');

                },
            ))
            ->add('position', 'text', array(
                'label' => 'Stilling',
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ExecutiveBoardMember',
        ));
    }

    public function getName()
    {
        return 'createExecutiveBoardMember';
    }
}

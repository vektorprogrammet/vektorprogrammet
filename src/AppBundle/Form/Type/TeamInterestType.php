<?php

namespace AppBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamInterestType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $department = $options['department'];

        $builder
            ->add('name', TextType::class, array(
                'label' => 'Navn'
            ))
            ->add('email', EmailType::class, array(
                'label' => 'Email'
            ))
            ->add('potentialTeams', EntityType::class, array(
                'label' => 'Hvilke team er du interessert i?',
                'class' => 'AppBundle:Team',
                'query_builder' => function (EntityRepository $entityRepository) use ($department) {
                    return $entityRepository->createQueryBuilder('team')
                        ->select('team')
                        ->where('team.department = :department')
                        ->andWhere('team.acceptApplication = true')
                        ->setParameter('department', $department);
                },
                'expanded' => true,
                'multiple' => true,
            ));
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\TeamInterest',
            'department' => null,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_teaminterest';
    }
}

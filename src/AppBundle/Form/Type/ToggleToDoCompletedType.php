<?php

namespace AppBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use function PHPSTORM_META\type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use AppBundle\Entity\Department;

class ToggleToDoCompletedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $option)
    {

        $builder
            ->add('department', 'entity', array(
                'type' => 'hidden',
            ))
            ->add('semester', 'entity', array(
                'type' => 'hidden',
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Model\ToDoCompleted',
        ));
    }

    public function getName()
    {
        return 'getToDoCompletedStatus';
    }
}

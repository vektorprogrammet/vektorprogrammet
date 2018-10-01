
<?php
/*
namespace AppBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AppBundle\Entity\Department;

class CreateToDoItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            /*
            ->add('createdAt', 'datetime', array(
                'label' => 'Når ble denne laget?',
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy HH:mm',
                'required' => true,
            ))
            ->add('deletedAt', 'datetime', array(
                'label' => 'Når ble denne slettet?',
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy HH:mm',
                'required' => true,
            ))* /
            ->add('toDoMandatory', 'choice', array(
                'label' => 'Hva er dette punktets prioritet?',
                'choices' => array(
                    true = "Jepp",
                    false = "nej",
                ),
                'expanded' => false,
                'required' => true,
            ))
            ->add('title', 'text', array(
                'label' => 'Hva er denne sin tittel?',
                //'widget' => 'single_text',
                'required' => true,
            ))
            ->add('description', 'text', array(
                'label' => 'Beskrivelse av gjøremålet?',
                //'widget' => 'single_text',
                'required' => true,
            ))
            ->add('department', 'entity', array(
                'label' => 'Hvilken department skal denne gjelde for?',
                'class' => 'AppBundle:Department',
                'query_builder' => function (EntityRepository $er){
                    return $er->createQueryBuilder('d')
                        ->orderBy('d.name', 'ASC');
                },
            ))
            ->add('semester', 'entity', array(
                'label' => 'Hvilket semester skal denne gjelde for?',
                'class' => 'AppBundle:Semester',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.semesterStartDate', 'ASC');
                },
            ))
            ->add('save', 'submit', array(
                'label' => 'Opprett',
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ToDoItem',
        ));
    }

    public function getName()
    {
        return 'createToDoItem';
    }
}



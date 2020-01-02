<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\User;
use AppBundle\Entity\UserGroupCollection;
use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use AppBundle\Entity\Team;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserGroupCollectionType extends AbstractType
{
    private $isEdit;
    private $bolkNames;
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->isEdit =  $options['isEdit'];
        $this->bolkNames =  $options['bolkNames'];

        $builder
            ->add("name", TextType::class, [
                'label' => 'Navn på inndeling',
                'disabled' => $this->isEdit


            ])

            ->add("numberUserGroups", IntegerType::class, [
                'label' => "Antall inndelinger brukere skal deles inn i",
                'disabled' => $this->isEdit

            ])


            ->add("semesters", EntityType::class, [
                'label' => false,
                "expanded" => true,
                "multiple" => true,
                "class" => Semester::class,
                'disabled' => $this->isEdit

            ])
            ->add("teams", EntityType::class, [
                'label' => false,
                "expanded" => true,
                "multiple" => true,
                "class" => Team::class,
                "group_by" => "department.city",
                'disabled' => $this->isEdit

            ])

            ->add("users", EntityType::class, [
                'label' => false,
                "expanded" => false,
                "multiple" => true,
                "class" => User::class,
                "group_by" => "fieldOfStudy.department",
                'required' => false,
                'disabled' => $this->isEdit

            ])

            ->add("assistantsDepartments", EntityType::class, [
                'label' => false,
                "expanded" => true,
                "multiple" => true,
                "class" => Department::class,
                'disabled' => $this->isEdit

            ])

            ->add(
                'assistantBolks',
                ChoiceType::class,
                [
                'label' => 'Bolk',
                'multiple' => true,
                'expanded' => true,
                'choices' =>  $this->bolkNames,
                'disabled' => $this->isEdit

            ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => UserGroupCollection::class,
            'bolkNames' => array(),
            'isEdit' => true,

        ]);
    }

    public function getName()
    {
        return 'app_bundle_usergroup_collection_type';
    }
}

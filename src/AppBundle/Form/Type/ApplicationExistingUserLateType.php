<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Application;
use AppBundle\Entity\Repository\DepartmentRepository;
use AppBundle\Entity\Repository\SemesterRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\CallbackTransformer;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicationExistingUserLateType extends ApplicationExistingUserType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('user', SearchAssistantType::class, [
            'entityManager' => $options['entityManager'],
            'constraints' => array(new NotNull(["message" => "Assistent må velges."])),
            'assistants' => $options['assistants'],
            'label' => 'Velg assistent',
        ]);

        parent::buildForm($builder, $options);
        $builder->remove('yearsOfStudy');
        $builder->remove('teamInterest');
        $builder->remove('potentialTeams');

        $builder->add('Send inn', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Application',
            'entityManager' => null,
            'assistants' => null,
            'teams' => null,
            'admissionManager' => null,
        ));
    }
}

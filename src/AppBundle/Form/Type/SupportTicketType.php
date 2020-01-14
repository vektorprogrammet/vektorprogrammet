<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Department;
use AppBundle\Entity\Repository\DepartmentRepository;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SupportTicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /**
         * @var DepartmentRepository $departmentRepository
         */
        $departmentRepository = $options['department_repository'];
        $builder->add('name', TextType::class, array(
            'label' => 'Ditt navn',
            'attr' => array(
                'autocomplete' => 'name'
            ),
            ));
        $builder->add('email', EmailType::class, array(
            'label' => 'Din e-post',
            'attr' => array(
                'autocomplete' => 'email'
            ),
            ));
        $builder->add('subject', TextType::class, array(
            'label' => 'Emne'));
        $builder->add('department', HiddenType::class, array(
            'label' => false));
        $builder->add('body', TextareaType::class, array(
            'label' => 'Melding',
            'attr' => array(
                'rows' => '9',
            ),
        ));
        $builder->add('recaptcha', EWZRecaptchaType::class, [
            'label' => false,
            'mapped' => false,
            'constraints' => array(
                new RecaptchaTrue()
            )
        ]);
        $builder->add('submit', SubmitType::class, array(
            'label' => 'Send melding',
            'attr' => array(
                'class' => 'btn-primary'
            )));

        $builder->get('department')
            ->addModelTransformer(new CallbackTransformer(
                function (Department $department) {
                    return $department->getId();
                },
                function ($id) use ($departmentRepository) {
                    return $departmentRepository->find($id);
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\SupportTicket',
            'department_repository' => null
        ));
    }

    public function getBlockPrefix()
    {
        return 'support_ticket';
    }
}

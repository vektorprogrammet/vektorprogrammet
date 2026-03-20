<?php

namespace App\Content\Form;

use App\Entity\Department;
use App\Entity\Repository\DepartmentRepository;
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
        $builder->add('name', TextType::class, [
            'label' => 'Ditt navn',
            'attr' => [
                'autocomplete' => 'name',
            ],
        ]);
        $builder->add('email', EmailType::class, [
            'label' => 'Din e-post',
            'attr' => [
                'autocomplete' => 'email',
            ],
        ]);
        $builder->add('subject', TextType::class, [
            'label' => 'Emne']);
        $builder->add('department', HiddenType::class, [
            'label' => false]);
        $builder->add('body', TextareaType::class, [
            'label' => 'Melding',
            'attr' => [
                'rows' => '9',
            ],
        ]);
        $builder->add('recaptcha', EWZRecaptchaType::class, [
            'label' => false,
            'mapped' => false,
            'constraints' => [
                new RecaptchaTrue(),
            ],
        ]);
        $builder->add('submit', SubmitType::class, [
            'label' => 'Send melding',
            'attr' => [
                'class' => 'btn-primary',
            ]]);

        $builder->get('department')
            ->addModelTransformer(new CallbackTransformer(
                fn (Department $department) => $department->getId(),
                fn ($id) => $departmentRepository->find($id)
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => \App\Content\Infrastructure\Entity\SupportTicket::class,
            'department_repository' => null,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'support_ticket';
    }
}

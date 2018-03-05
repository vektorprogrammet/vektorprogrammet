<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Department;
use AppBundle\Entity\Repository\DepartmentRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SupportTicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /**
         * @var DepartmentRepository $departmentRepository
         */
        $departmentRepository = $options['department_repository'];
        $builder->add('name', 'text', array(
            'label' => 'Ditt navn',
            'attr' => array(
                'autocomplete' => 'name'
            ),
            ));
        $builder->add('email', 'email', array(
            'label' => 'Din e-post',
            'attr' => array(
                'autocomplete' => 'email'
            ),
            ));
        $builder->add('subject', 'text', array(
            'label' => 'Emne'));
        $builder->add('department', 'hidden', array(
            'label' => false));
        $builder->add('body', 'textarea', array(
            'label' => 'Melding',
            'attr' => array(
                'rows' => '9',
            ),
        ));
        $builder->add('submit', 'submit', array(
            'label' => 'Send melding',
            'attr' => array(
                'class' => 'btn-primary'
            )));
        $builder->add('captcha', 'captcha', array(
            'label' => ' ',
            'width' => 200,
            'height' => 50,
            'length' => 5,
            'quality' => 200,
            'keep_value' => true,
            'distortion' => false,
            'background_color' => [255, 255, 255],
        ));

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

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\SupportTicket',
            'department_repository' => null
        ));
    }

    public function getName()
    {
        return 'support_ticket';
    }
}

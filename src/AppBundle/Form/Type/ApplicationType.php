<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Department;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ApplicationType extends AbstractType
{
    private $authenticated;
    private $departmentId;

    public function __construct(Department $department, $authenticated)
    {
        $this->departmentId = $department->getId();
        $this->authenticated = $authenticated;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        // The fields that populate the form
        $builder
            ->add('user', new CreateUserOnApplicationType($this->departmentId), array(
                'label' => '',
            ))
            ->add('yearOfStudy', 'choice', array(
                'label' => 'Årstrinn',
                'choices' => array(
                    1 => '1',
                    2 => '2',
                    3 => '3',
                    4 => '4',
                    5 => '5',
                ),
            ))
            ->add('save', 'submit', array('label' => 'Søk nå!'));

        /*
        See options for configuration here:
        https://github.com/Gregwar/CaptchaBundle
        */
        if (!$this->authenticated) {
            $builder->add('captchaAdmission', 'captcha', array(
                'label' => ' ',
                'width' => 200,
                'height' => 50,
                'length' => 5,
                'quality' => 200,
                'keep_value' => true,
                'distortion' => false,
                'background_color' => [111, 206, 238],
            ));
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Application',
            'user' => null,
        ));
    }

    public function getName()
    {
        return 'application'; // This must be unique
    }
}

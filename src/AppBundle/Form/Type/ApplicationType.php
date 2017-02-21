<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ApplicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        // The fields that populate the form
        $builder
            ->add('user', new CreateUserOnApplicationType($options['departmentId']), array(
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
            ->add('save', 'submit', array('label' => 'Søk nå!'))
            /*
            See options for configuration here:
            https://github.com/Gregwar/CaptchaBundle
            */
            ->add('captchaAdmission', 'captcha', array(
                'disabled' => $options['environment'] === 'test',
                'label' => ' ',
                'width' => 200,
                'height' => 50,
                'length' => 5,
                'quality' => 200,
                'keep_value' => true,
                'distortion' => false,
                'background_color' => [111, 206, 238],
            ))
            ->add('wantsNewsletter', CheckboxType::class, array(
                'label' => 'Send meg informasjon om opptak på epost',
                'attr' => array('checked' => 'checked'),
                'required' => false,
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Application',
            'user' => null,
            'departmentId' => null,
            'environment' => 'prod',
        ));
    }

    public function getName()
    {
        return 'application'; // This must be unique
    }
}

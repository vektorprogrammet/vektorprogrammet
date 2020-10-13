<?php

namespace AppBundle\Form\Type;

use AppBundle\AssistantScheduling\Assistant;
use AppBundle\Entity\Repository\SemesterRepository;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;

class SearchAssistantType extends AbstractType
{
    private $entityManager;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->entityManager = $options["entityManager"];
        $this->assistants = $options["assistants"];
        $builder->add('id', HiddenType::class);

        $builder->addModelTransformer(new CallbackTransformer(
            function ($user){
                if($user){
                    return $user->getId();
                }
                return null;
            },
            function ($userid){
                if($userid['id']){
                    $user = $this->entityManager->getRepository('AppBundle:User')->find($userid['id']);
                    return $user;
                }
                return null;
            }));
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'entityManager' => null,
            'assistants' => null,
            'constraints' => array(new Valid()),
        ]);
    }
    public function buildView($view, $form, array $options)
    {
        $view->vars = array_replace($view->vars, array(
            'assistants' => $this->assistants,
        ));
    }
}


<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class InterviewAnswerType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        dump($builder);
        // Have to use form events to access entity properties in an embedded form
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use (&$interviewAnswer) {
            $interviewAnswer = $event->getData();
            dump($interviewAnswer);
            dump($interviewAnswer->getInterviewQuestion());
            $form = $event->getForm();

            // Add different form fields depending on the type of the interview question
            switch($interviewAnswer->getInterviewQuestion()->getType()) {
                case "list": // This creates a dropdown list if the type is list
                    $choices = $this->createChoices($interviewAnswer);

                    $form->add('answer', 'choice', array(
                        'label' => $interviewAnswer->getInterviewQuestion()->getQuestion(),
                        'help' => $interviewAnswer->getInterviewQuestion()->getHelp(),
                        'choices' => $choices
                    ));

                    break;
                case "radio": // This creates a set of radio buttons if the type is radio
                    $choices = $this->createChoices($interviewAnswer);

                    $form->add('answer', 'choice', array(
                        'label' => $interviewAnswer->getInterviewQuestion()->getQuestion(),
                        'help' => $interviewAnswer->getInterviewQuestion()->getHelp(),
                        'choices' => $choices,
                        'expanded' => true
                    ));
                    break;
                case "check": // This creates a set of checkboxes if the type is check
                    $choices = $this->createChoices($interviewAnswer);

                    $form->add('answer', 'choice', array(
                        'label' => $interviewAnswer->getInterviewQuestion()->getQuestion(),
                        'help' => $interviewAnswer->getInterviewQuestion()->getHelp(),
                        'choices' => $choices,
                        'expanded' => true,
                        'multiple' => true
                    ));
                    break;
                default: // This creates a textarea if the type is text (default)
                    $type = "text";
                    $form->add('answer', 'textarea', array(
                        'label' => $interviewAnswer->getInterviewQuestion()->getQuestion(),
                        'help' => $interviewAnswer->getInterviewQuestion()->getHelp()
                    ));
            }
        });

        // If the user supplied a new value to a choice field, this new value must be added as one of the choices
        // in order for the form to validate, as values other than the specified choices are not allowed.
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) use (&$interviewAnswer) {
            $form = $event->getForm();

            // This is the data submitted in the form
            $data = $event->getData();

            // Remove and then add the form fields with the new choices.
            switch($interviewAnswer->getInterviewQuestion()->getType()) {
                case "list":
                    $choices = $this->createChoices($interviewAnswer);

                    // Add the new value to the choice array
                    $choices[$data['answer']] = $data['answer'];

                    $form->remove('answer');
                    $form->add('answer', 'choice', array(
                        'label' => $interviewAnswer->getInterviewQuestion()->getQuestion(),
                        'help' => $interviewAnswer->getInterviewQuestion()->getHelp(),
                        'choices' => $choices
                    ));

                    break;
                case "radio":
                    $choices = $this->createChoices($interviewAnswer);

                    // Add the new value to the choice array
                    $choices[$data['answer']] = $data['answer'];

                    $form->remove('answer');
                    $form->add('answer', 'choice', array(
                        'label' => $interviewAnswer->getInterviewQuestion()->getQuestion(),
                        'help' => $interviewAnswer->getInterviewQuestion()->getHelp(),
                        'choices' => $choices,
                        'expanded' => true
                    ));
                    break;
                case "check":
                    $choices = $this->createChoices($interviewAnswer);

                    // Add the new value to the choice array
                    // The data from the form is an array (because it's checkboxes) in this case
                    $answers = array_combine($data['answer'],$data['answer']);
                    $newChoices = array_merge($choices, $answers);

                    $form->remove('answer');
                    $form->add('answer', 'choice', array(
                        'label' => $interviewAnswer->getInterviewQuestion()->getQuestion(),
                        'help' => $interviewAnswer->getInterviewQuestion()->getHelp(),
                        'choices' => $newChoices,
                        'expanded' => true,
                        'multiple' => true
                    ));
                    break;
            }
        });
    }

    /**
     * Creates a key value array of alternatives from a Doctrine collection of QuestionAlternatives.
     * The key and the value are the same.
     *
     * @param $interviewAnswer
     * @return array
     */
    public function createChoices($interviewAnswer) {
        $alternatives = $interviewAnswer->getInterviewQuestion()->getAlternatives();

        $values = array_map(function($a) { return $a->getAlternative(); }, $alternatives->getValues());

        return array_combine($values, $values);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\InterviewAnswer',
        ));
    }

    public function getName()
    {
        return 'interviewAnswer';
    }
}
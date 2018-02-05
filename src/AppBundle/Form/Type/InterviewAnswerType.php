<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\InterviewAnswer;
use AppBundle\Entity\InterviewQuestionAlternative;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class InterviewAnswerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Have to use form events to access entity properties in an embedded form
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use (&$interviewAnswer) {
            $interviewAnswer = $event->getData();
            $form = $event->getForm();

            // Add different form fields depending on the type of the interview question
            switch ($interviewAnswer->getInterviewQuestion()->getType()) {
                case 'list': // This creates a dropdown list if the type is list
                    $choices = $this->createChoices($interviewAnswer);

                    $form->add('answer', 'choice', array(
                        'label' => $interviewAnswer->getInterviewQuestion()->getQuestion(),
                        'help' => $interviewAnswer->getInterviewQuestion()->getHelp(),
                        'choices' => $choices,
                    ));

                    break;
                case 'radio': // This creates a set of radio buttons if the type is radio
                    $choices = $this->createChoices($interviewAnswer);

                    $form->add('answer', 'choice', array(
                        'label' => $interviewAnswer->getInterviewQuestion()->getQuestion(),
                        'help' => $interviewAnswer->getInterviewQuestion()->getHelp(),
                        'choices' => $choices,
                        'expanded' => true,
                    ));
                    break;
                case 'check': // This creates a set of checkboxes if the type is check
                    $choices = $this->createChoices($interviewAnswer);

                    $form->add('answer', 'choice', array(
                        'label' => $interviewAnswer->getInterviewQuestion()->getQuestion(),
                        'help' => $interviewAnswer->getInterviewQuestion()->getHelp(),
                        'choices' => $choices,
                        'expanded' => true,
                        'multiple' => true,
                    ));
                    break;
                default: // This creates a textarea if the type is text (default)
                    $form->add('answer', 'textarea', array(
                        'label' => $interviewAnswer->getInterviewQuestion()->getQuestion(),
                        'help' => $interviewAnswer->getInterviewQuestion()->getHelp(),
                    ));
            }
        });
    }

    /**
     * Creates a key value array of alternatives from a Doctrine collection of QuestionAlternatives.
     * The key and the value are the same.
     *
     * @param InterviewAnswer $interviewAnswer
     *
     * @return array
     */
    public function createChoices(InterviewAnswer $interviewAnswer)
    {
        $alternatives = $interviewAnswer->getInterviewQuestion()->getAlternatives();

        $values = array_map(function (InterviewQuestionAlternative $a) {
            return $a->getAlternative();
        }, $alternatives->getValues());

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

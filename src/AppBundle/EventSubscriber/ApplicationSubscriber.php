<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Event\ApplicationCreatedEvent;
use AppBundle\Service\AdmissionNotifier;
use AppBundle\Mailer\MailerInterface;
use AppBundle\Service\UserRegistration;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Twig\Environment;

class ApplicationSubscriber implements EventSubscriberInterface
{
    private $mailer;
    private $twig;
    private $admissionNotifier;
    /**
     * @var UserRegistration
     */
    private $userRegistrationService;

    /**
     * ApplicationAdmissionSubscriber constructor.
     *
     * @param MailerInterface $mailer
     * @param Environment $twig
     * @param AdmissionNotifier $admissionNotifier
     * @param UserRegistration $userRegistrationService
     */
    public function __construct(MailerInterface $mailer, Environment $twig, AdmissionNotifier $admissionNotifier, UserRegistration $userRegistrationService)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->admissionNotifier = $admissionNotifier;
        $this->userRegistrationService = $userRegistrationService;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            ApplicationCreatedEvent::NAME => array(
                array( 'sendConfirmationMail', 0 ),
                array( 'createAdmissionSubscriber', - 2 ),
            ),
        );
    }

    public function createAdmissionSubscriber(ApplicationCreatedEvent $event)
    {
        $application = $event->getApplication();
        $department = $application->getUser()->getDepartment();
        $email = $application->getUser()->getEmail();
        try {
            $this->admissionNotifier->createSubscription($department, $email, true);
        } catch (\Exception $e) {
            // Ignore
        }
    }

    public function sendConfirmationMail(ApplicationCreatedEvent $event)
    {
        $application = $event->getApplication();
        $user = $application->getUser();
        $newUserCode = null;
        if (!$user->getPassword()) {
            $newUserCode = $this->userRegistrationService->setNewUserCode($user);
        }

        $template = 'admission/admission_email.html.twig';
        if ($application->getUser()->hasBeenAssistant()) {
            $template = 'admission/admission_existing_email.html.twig';
        }

        // Send a confirmation email with a copy of the application
        $emailMessage = (new \Swift_Message())
                                      ->setSubject('Søknad - Vektorassistent')
                                      ->setReplyTo($application->getDepartment()->getEmail())
                                      ->setTo($application->getUser()->getEmail())
                                      ->setBody($this->twig->render($template, array(
                                          'application'   => $application,
                                          'new_user_code' => $newUserCode
                                      )), 'text/html');

        $this->mailer->send($emailMessage);
    }
}

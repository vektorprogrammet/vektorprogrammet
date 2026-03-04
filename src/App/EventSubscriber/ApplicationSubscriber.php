<?php

namespace App\EventSubscriber;

use App\Event\ApplicationCreatedEvent;
use App\Service\AdmissionNotifier;
use App\Mailer\MailerInterface;
use App\Service\UserRegistration;
use Exception;
use Symfony\Component\Mime\Email;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Twig\Environment;

class ApplicationSubscriber implements EventSubscriberInterface
{
    private $mailer;
    private $twig;
    private $admissionNotifier;
    private $userRegistrationService;

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
        } catch (Exception $e) {
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
        $emailMessage = (new Email())
                                      ->subject('Søknad - Vektorassistent')
                                      ->replyTo($application->getDepartment()->getEmail())
                                      ->to($application->getUser()->getEmail())
                                      ->html($this->twig->render($template, array(
                                          'application'   => $application,
                                          'new_user_code' => $newUserCode
                                      )));

        $this->mailer->send($emailMessage);
    }
}

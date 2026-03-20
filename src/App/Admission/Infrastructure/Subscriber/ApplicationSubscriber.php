<?php

namespace App\Admission\Infrastructure\Subscriber;

use App\Admission\Domain\Events\ApplicationCreatedEvent;
use App\Support\Infrastructure\Mailer\MailerInterface;
use App\Admission\Infrastructure\AdmissionNotifier;
use App\Service\UserRegistration;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class ApplicationSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly MailerInterface $mailer, private readonly Environment $twig, private readonly AdmissionNotifier $admissionNotifier, private readonly UserRegistration $userRegistrationService)
    {
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            ApplicationCreatedEvent::NAME => [
                ['sendConfirmationMail', 0],
                ['createAdmissionSubscriber', -2],
            ],
        ];
    }

    public function createAdmissionSubscriber(ApplicationCreatedEvent $event)
    {
        $application = $event->getApplication();
        $department = $application->getUser()->getDepartment();
        $email = $application->getUser()->getEmail();
        try {
            $this->admissionNotifier->createSubscription($department, $email, true);
        } catch (\Exception) {
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
                                      ->html($this->twig->render($template, [
                                          'application' => $application,
                                          'new_user_code' => $newUserCode,
                                      ]));

        $this->mailer->send($emailMessage);
    }
}

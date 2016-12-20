<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Event\ApplicationCreatedEvent;
use Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class ApplicationSubscriber implements EventSubscriberInterface
{
    private $mailer;
    private $twig;
    private $session;
    private $logger;

    /**
     * ApplicationAdmissionSubscriber constructor.
     *
     * @param \Swift_Mailer     $mailer
     * @param \Twig_Environment $twig
     * @param Session           $session
     * @param Logger            $logger
     */
    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig, Session $session, Logger $logger)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->session = $session;
        $this->logger = $logger;
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
                array('logApplication', 1),
                array('sendConfirmationMail', 0),
                array('addFlashMessage', -1),
            ),
        );
    }

    public function sendConfirmationMail(ApplicationCreatedEvent $event)
    {
        $application = $event->getApplication();

        $template = 'admission/admission_email.html.twig';
        if ($application->getUser()->hasBeenAssistant()) {
            $template = 'admission/admission_existing_email.html.twig';
        }

        // Send a confirmation email with a copy of the application
        $emailMessage = \Swift_Message::newInstance()
            ->setSubject('Søknad - Vektorassistent')
            ->setFrom(array('rekruttering@vektorprogrammet.no' => 'Vektorprogrammet'))
            ->setTo($application->getUser()->getEmail())
            ->setBody($this->twig->render($template, array('application' => $application)));

        $this->mailer->send($emailMessage);

        $this->logger->info("Application confirmation mail sent to {$application->getUser()->getEmail()}");
    }

    public function addFlashMessage(ApplicationCreatedEvent $event)
    {
        $application = $event->getApplication();
        $message = "Søknaden din er registrert. En kvittering har blitt sendt til {$application->getUser()->getEmail()}. Lykke til!";

        $this->session->getFlashBag()->add('admission-notice', $message);
    }

    public function logApplication(ApplicationCreatedEvent $event)
    {
        $application = $event->getApplication();

        $this->logger->info("New application from {$application->getUser()} registered");
    }
}

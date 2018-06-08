<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Event\ApplicationCreatedEvent;
use AppBundle\Service\AdmissionNotifier;
use AppBundle\Service\ApplicationData;
use AppBundle\Mailer\MailerInterface;
use AppBundle\Service\NewsletterManager;
use AppBundle\Service\SlackMessenger;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;

class ApplicationSubscriber implements EventSubscriberInterface
{
    private $mailer;
    private $twig;
    private $session;
    private $logger;
    private $slackMessenger;
    private $applicationData;
    private $router;
    private $admissionNotifier;

    /**
     * ApplicationAdmissionSubscriber constructor.
     *
     * @param MailerInterface $mailer
     * @param \Twig_Environment $twig
     * @param Session $session
     * @param LoggerInterface $logger
     * @param SlackMessenger $slackMessenger
     * @param ApplicationData $applicationData
     * @param RouterInterface $router
     */
    public function __construct(MailerInterface $mailer, \Twig_Environment $twig, Session $session, LoggerInterface $logger, SlackMessenger $slackMessenger, ApplicationData $applicationData, RouterInterface $router, AdmissionNotifier $admissionNotifier)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->session = $session;
        $this->logger = $logger;
        $this->slackMessenger = $slackMessenger;
        $this->applicationData = $applicationData;
        $this->router = $router;
        $this->admissionNotifier = $admissionNotifier;
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
                array( 'logApplication', 1 ),
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

        $template = 'admission/admission_email.html.twig';
        if ($application->getUser()->hasBeenAssistant()) {
            $template = 'admission/admission_existing_email.html.twig';
        }

        // Send a confirmation email with a copy of the application
        $emailMessage = \Swift_Message::newInstance()
                                      ->setSubject('Søknad - Vektorassistent')
                                      ->setFrom(array( 'rekruttering@vektorprogrammet.no' => 'Vektorprogrammet' ))
                                      ->setTo($application->getUser()->getEmail())
                                      ->setBody($this->twig->render($template, array( 'application' => $application )), 'text/html');

        $this->mailer->send($emailMessage);

        $this->logger->info("Application confirmation mail sent to {$application->getUser()->getEmail()}");
    }

    public function logApplication(ApplicationCreatedEvent $event)
    {
        $application = $event->getApplication();

        $user = $application->getUser();
        $department = $user->getDepartment();
        $this->applicationData->setDepartment($department);

        $this->logger->info("$department: New application from *$user* registered. $department has *{$this->applicationData->getApplicationCount()}* applicants");

        $this->slackMessenger->notify("$department: Ny søknad fra *$user* registrert. $department har nå *{$this->applicationData->getApplicationCount()}* søkere: "
                                       . $this->router->generate('applications_show_by_semester', array( 'id' => $department->getCurrentOrLatestSemester()->getId() ), RouterInterface::ABSOLUTE_URL));
    }
}

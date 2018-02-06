<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Event\TeamApplicationCreatedEvent;
use AppBundle\Service\MailerInterface;
use AppBundle\Service\SlackMessenger;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;

class TeamApplicationSubscriber implements EventSubscriberInterface
{
    private $mailer;
    private $twig;
    private $session;
    private $logger;
    private $slackMessenger;
    private $router;

    /**
     * ApplicationAdmissionSubscriber constructor.
     *
     * @param MailerInterface   $mailer
     * @param \Twig_Environment $twig
     * @param Session           $session
     * @param LoggerInterface   $logger
     * @param SlackMessenger    $slackMessenger
     * @param RouterInterface   $router
     */
    public function __construct(MailerInterface $mailer, \Twig_Environment $twig, Session $session, LoggerInterface $logger, SlackMessenger $slackMessenger, RouterInterface $router)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->session = $session;
        $this->logger = $logger;
        $this->slackMessenger = $slackMessenger;
        $this->router = $router;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            TeamApplicationCreatedEvent::NAME => array(
                array('logEvent', 1),
                array('sendConfirmationMail', 0),
                array('sendApplicationToTeamMail', 0),
                array('addFlashMessage', -1),
            ),
        );
    }

    public function sendConfirmationMail(TeamApplicationCreatedEvent $event)
    {
        $application = $event->getTeamApplication();
        $team = $application->getTeam();

        if (null === $email = $team->getEmail()) {
            $email = $team->getDepartment()->getEmail();
        }

        $receipt = \Swift_Message::newInstance()
            ->setSubject('Søknad til '.$team->getName().' mottatt')
            ->setFrom(array($email => $team->getName()))
            ->setReplyTo($email)
            ->setTo($application->getEmail())
            ->setBody($this->twig->render('team/receipt.html.twig', array(
                'team' => $team,
            )));
        $this->mailer->send($receipt);
    }

    public function sendApplicationToTeamMail(TeamApplicationCreatedEvent $event)
    {
        $application = $event->getTeamApplication();
        $team = $application->getTeam();

        if (null === $email = $team->getEmail()) {
            $email = $team->getDepartment()->getEmail();
        }

        $receipt = \Swift_Message::newInstance()
            ->setSubject('Ny søker til '.$team->getName())
            ->setFrom(array('vektorprogrammet@vektorprogrammet.no' => 'Vektorprogrammet'))
            ->setReplyTo($application->getEmail())
            ->setTo($email)
            ->setBody($this->twig->render('team/application_email.html.twig', array(
                'application' => $application,
            )));
        $this->mailer->send($receipt);
    }

    public function addFlashMessage()
    {
        $this->session->getFlashBag()->add('success', 'Søknaden er mottatt.');
    }

    public function logEvent(TeamApplicationCreatedEvent $event)
    {
        $application = $event->getTeamApplication();

        $this->logger->info("New team({$application->getTeam()}) application from {$application->getName()} registered");

        $department = $application->getTeam()->getDepartment();

        $this->slackMessenger->notify("$department: *{$application->getName()}* har sendt en søknad til {$application->getTeam()}. Les søknaden her: "
            .$this->router->generate('team_application_show', array('id' => $application->getId()), RouterInterface::ABSOLUTE_URL));
    }
}

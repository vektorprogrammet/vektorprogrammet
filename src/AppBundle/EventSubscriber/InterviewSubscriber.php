<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Event\InterviewConductedEvent;
use Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class InterviewSubscriber implements EventSubscriberInterface
{
    private $mailer;
    private $twig;
    private $session;
    private $logger;
    private $tokenStorage;

    /**
     * ApplicationAdmissionSubscriber constructor.
     *
     * @param \Swift_Mailer     $mailer
     * @param \Twig_Environment $twig
     * @param Session           $session
     * @param Logger            $logger
     * @param TokenStorage      $tokenStorage
     */
    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig, Session $session, Logger $logger, TokenStorage $tokenStorage)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->session = $session;
        $this->logger = $logger;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            InterviewConductedEvent::NAME => array(
                array('logEvent', 1),
                array('sendInterviewReceipt', 0),
                array('addFlashMessage', -1),
            ),
        );
    }

    public function sendInterviewReceipt(InterviewConductedEvent $event)
    {
        $application = $event->getApplication();

        $interviewer = $this->tokenStorage->getToken()->getUser();

        // Send email to the interviewee with a summary of the interview
        $emailMessage = \Swift_Message::newInstance()
            ->setSubject('Vektorprogrammet intervju')
            ->setFrom(array('rekruttering@vektorprogrammet.no' => 'Vektorprogrammet'))
            ->setTo($application->getUser()->getEmail())
            ->setReplyTo($interviewer->getEmail())
            ->setBody($this->twig->render('interview/interview_summary_email.html.twig', array(
                'application' => $application,
                'interviewer' => $interviewer,
            )));
        $this->mailer->send($emailMessage);

        $this->logger->info("Interview receipt sent to {$application->getUser()} at {$application->getUser()->getEmail()}");
    }

    public function addFlashMessage(InterviewConductedEvent $event)
    {
        $message = "Intervjuet med {$event->getApplication()->getUser()} ble lagret.";

        $this->session->getFlashBag()->add('success', $message);
    }

    public function logEvent(InterviewConductedEvent $event)
    {
        $application = $event->getApplication();

        $this->logger->info("New interview with {$application->getUser()} registered");
    }
}

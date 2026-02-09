<?php

namespace App\EventSubscriber;

use App\Event\TeamApplicationCreatedEvent;
use App\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class TeamApplicationSubscriber implements EventSubscriberInterface
{
    private $mailer;
    private $twig;
    private $requestStack;

    public function __construct(MailerInterface $mailer, Environment $twig, RequestStack $requestStack)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->requestStack = $requestStack;
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

        $receipt = (new Email())
            ->subject('Søknad til '.$team->getName().' mottatt')
            ->from(new Address($email, $team->getName()))
            ->replyTo($email)
            ->to($application->getEmail())
            ->text($this->twig->render('team/receipt.html.twig', array(
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

        $receipt = (new Email())
            ->subject('Ny søker til '.$team->getName())
            ->from(new Address('vektorprogrammet@vektorprogrammet.no', 'Vektorprogrammet'))
            ->replyTo($application->getEmail())
            ->to($email)
            ->text($this->twig->render('team/application_email.html.twig', array(
                'application' => $application,
            )));
        $this->mailer->send($receipt);
    }

    public function addFlashMessage()
    {
        $this->requestStack->getSession()->getFlashBag()->add('success', 'Søknaden er mottatt.');
    }
}

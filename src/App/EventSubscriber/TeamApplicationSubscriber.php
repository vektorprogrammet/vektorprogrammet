<?php

namespace App\EventSubscriber;

use App\Event\TeamApplicationCreatedEvent;
use App\Support\Infrastructure\Mailer\MailerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class TeamApplicationSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly MailerInterface $mailer, private readonly Environment $twig, private readonly RequestStack $requestStack)
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
            TeamApplicationCreatedEvent::NAME => [
                ['sendConfirmationMail', 0],
                ['sendApplicationToTeamMail', 0],
                ['addFlashMessage', -1],
            ],
        ];
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
            ->text($this->twig->render('team/receipt.html.twig', [
                'team' => $team,
            ]));
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
            ->text($this->twig->render('team/application_email.html.twig', [
                'application' => $application,
            ]));
        $this->mailer->send($receipt);
    }

    public function addFlashMessage()
    {
        $this->requestStack->getSession()->getFlashBag()->add('success', 'Søknaden er mottatt.');
    }
}

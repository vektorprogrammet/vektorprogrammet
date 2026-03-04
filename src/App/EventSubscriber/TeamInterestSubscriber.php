<?php

namespace App\EventSubscriber;

use App\Event\TeamInterestCreatedEvent;
use App\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class TeamInterestSubscriber implements EventSubscriberInterface
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

    public static function getSubscribedEvents()
    {
        return array(TeamInterestCreatedEvent::NAME => array(
            array('sendConfirmationMail', 0),
            array('addFlashMessage', -1),
        ));
    }

    public function sendConfirmationMail(TeamInterestCreatedEvent $event)
    {
        $teamInterest = $event->getTeamInterest();
        $department = $teamInterest->getDepartment();
        $fromEmail = $department->getEmail();

        $receipt = (new Email())
            ->subject("Teaminteresse i Vektorprogrammet")
            ->from(new Address($fromEmail, "Vektorprogrammet $department"))
            ->replyTo($fromEmail)
            ->to($teamInterest->getEmail())
            ->html($this->twig->render("team_interest/team_interest_receipt.html.twig", array(
                'teamInterest' => $teamInterest,
            )));
        $this->mailer->send($receipt);
    }

    public function addFlashMessage()
    {
        $this->requestStack->getSession()->getFlashBag()->add('success', 'Takk! Vi kontakter deg så fort som mulig.');
    }
}

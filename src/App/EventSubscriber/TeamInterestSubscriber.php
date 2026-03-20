<?php

namespace App\EventSubscriber;

use App\Event\TeamInterestCreatedEvent;
use App\Support\Infrastructure\Mailer\MailerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class TeamInterestSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly MailerInterface $mailer, private readonly Environment $twig, private readonly RequestStack $requestStack)
    {
    }

    public static function getSubscribedEvents()
    {
        return [TeamInterestCreatedEvent::NAME => [
            ['sendConfirmationMail', 0],
            ['addFlashMessage', -1],
        ]];
    }

    public function sendConfirmationMail(TeamInterestCreatedEvent $event)
    {
        $teamInterest = $event->getTeamInterest();
        $department = $teamInterest->getDepartment();
        $fromEmail = $department->getEmail();

        $receipt = (new Email())
            ->subject('Teaminteresse i Vektorprogrammet')
            ->from(new Address($fromEmail, "Vektorprogrammet $department"))
            ->replyTo($fromEmail)
            ->to($teamInterest->getEmail())
            ->html($this->twig->render('team_interest/team_interest_receipt.html.twig', [
                'teamInterest' => $teamInterest,
            ]));
        $this->mailer->send($receipt);
    }

    public function addFlashMessage()
    {
        $this->requestStack->getSession()->getFlashBag()->add('success', 'Takk! Vi kontakter deg så fort som mulig.');
    }
}

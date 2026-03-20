<?php

declare(strict_types=1);

namespace App\Organization\Infrastructure\Subscriber;

use App\Organization\Domain\Events\TeamInterestCreatedEvent;
use App\Support\Infrastructure\Mailer\MailerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class TeamInterestSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly MailerInterface $mailer, private readonly Environment $twig, private readonly RequestStack $requestStack)
    {
    }

    /**
     * @return array<string, list<array{0: string, 1?: int}|int|string>|string>
     */
    public static function getSubscribedEvents(): array
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

    public function addFlashMessage(): void
    {
        $session = $this->requestStack->getSession();
        if ($session instanceof FlashBagAwareSessionInterface) {
            $session->getFlashBag()->add('success', 'Takk! Vi kontakter deg så fort som mulig.');
        }
    }
}

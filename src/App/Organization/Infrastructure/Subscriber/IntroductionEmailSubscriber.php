<?php

namespace App\Organization\Infrastructure\Subscriber;

use App\Organization\Domain\Events\TeamMembershipEvent;
use App\Support\Infrastructure\Mailer\MailerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class IntroductionEmailSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly MailerInterface $mailer, private readonly Environment $twig)
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
            TeamMembershipEvent::CREATED => [
                ['sendWelcomeToTeamEmail', -1],
                ['sendGoogleEmail', -2],
            ],
        ];
    }

    public function sendWelcomeToTeamEmail(TeamMembershipEvent $event)
    {
        $teamMembership = $event->getTeamMembership();

        $team = $teamMembership->getTeam();
        $user = $teamMembership->getUser();

        if (count($user->getTeamMemberships()) > 1) {
            return;
        }

        $position = $teamMembership->getPositionName();

        $message = (new Email())
            ->subject('Velkommen til '.$team->getName())
            ->from('vektorbot@vektorprogrammet.no')
            ->to($user->getEmail())
            ->html($this->twig->render('team_admin/welcome_team_membership_mail.html.twig', [
                'name' => $user->getFirstName(),
                'team' => $team->getName(),
                'position' => $position,
                'companyEmail' => $user->getCompanyEmail(),
            ]));
        $this->mailer->send($message);
    }

    public function sendGoogleEmail(TeamMembershipEvent $event)
    {
        $teamMembership = $event->getTeamMembership();
        $user = $teamMembership->getUser();

        if (count($user->getTeamMemberships()) > 1) {
            return;
        }

        $message = (new Email())
            ->subject('Fullfør oppsettet med din Vektor-epost')
            ->from('vektorbot@vektorprogrammet.no')
            ->to($user->getCompanyEmail())
            ->html($this->twig->render('team_admin/welcome_google_mail.html.twig', [
                'name' => $user->getFirstName(),
            ]));
        $this->mailer->send($message);
    }
}

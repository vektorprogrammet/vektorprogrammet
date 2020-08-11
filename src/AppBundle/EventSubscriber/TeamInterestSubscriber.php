<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Event\TeamInterestCreatedEvent;
use AppBundle\Mailer\MailerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class TeamInterestSubscriber implements EventSubscriberInterface
{
    private $mailer;
    private $twig;
    private $session;

    /**
     * TeamInterestSubscriber constructor.
     *
     * @param MailerInterface $mailer
     * @param \Twig_Environment $twig
     * @param SessionInterface $session
     */
    public function __construct(MailerInterface $mailer, \Twig_Environment $twig, SessionInterface $session)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->session = $session;
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

        $receipt = (new \Swift_Message())
            ->setSubject("Teaminteresse i Vektorprogrammet")
            ->setFrom(array($fromEmail => "Vektorprogrammet $department"))
            ->setReplyTo($fromEmail)
            ->setTo($teamInterest->getEmail())
            ->setBody($this->twig->render(":team_interest:team_interest_receipt.html.twig", array(
                'teamInterest' => $teamInterest,
            )))
            ->setContentType('text/html');
        $this->mailer->send($receipt);
    }

    public function addFlashMessage()
    {
        $this->session->getFlashBag()->add('success', 'Takk! Vi kontakter deg så fort som mulig.');
    }
}

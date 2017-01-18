<?php

namespace AppBundle\Service;

use AppBundle\Entity\SupportTicket;
use Psr\Log\LoggerInterface;

class EmailSender
{
    private $mailer;
    private $twig;
    private $defaultEmail;
    private $logger;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig, LoggerInterface $logger, string $defaultEmail)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->defaultEmail = $defaultEmail;
        $this->logger = $logger;
    }

    public function sendSupportTicketToDepartment(SupportTicket $supportTicket)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Nytt kontaktskjema')
            ->setFrom($this->defaultEmail)
            ->setReplyTo($supportTicket->getEmail())
            ->setTo($supportTicket->getDepartment()->getEmail())
            ->setBody($this->twig->render('admission/contactEmail.txt.twig', array('contact' => $supportTicket)));
        $this->mailer->send($message);

        $this->logger->info(
            "Support ticket from {$supportTicket->getName()} sent to department {$supportTicket->getDepartment()} ".
            "at {$supportTicket->getDepartment()->getEmail()}"
        );
    }

    public function sendSupportTicketReceipt(SupportTicket $supportTicket)
    {
        $receipt = \Swift_Message::newInstance()
            ->setSubject('Kvittering for kontaktskjema')
            ->setFrom($this->defaultEmail)
            ->setReplyTo($supportTicket->getDepartment()->getEmail())
            ->setTo($supportTicket->getEmail())
            ->setBody($this->twig->render('admission/receiptEmail.txt.twig', array('contact' => $supportTicket)));
        $this->mailer->send($receipt);

        $this->logger->info(
            "Support ticket receipt sent to {$supportTicket->getName()} at {$supportTicket->getEmail()}"
        );
    }
}

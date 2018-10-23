<?php

namespace AppBundle\Service;

use AppBundle\Entity\AdmissionSubscriber;
use AppBundle\Entity\SupportTicket;
use AppBundle\Entity\Receipt;
use AppBundle\Mailer\MailerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Router;

class EmailSender
{
    private $mailer;
    private $twig;
    private $defaultEmail;
    private $logger;
    private $economyEmail;
    private $router;

    public function __construct(MailerInterface $mailer, \Twig_Environment $twig, Router $router, LoggerInterface $logger, string $defaultEmail, string $economyEmail)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->defaultEmail = $defaultEmail;
        $this->logger = $logger;
        $this->economyEmail = $economyEmail;
        $this->router = $router;
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
    }

    public function sendPaidReceiptConfirmation(Receipt $receipt)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Vi har tilbakebetalt penger for utlegget ditt')
            ->setFrom($this->economyEmail)
            ->setFrom(array($this->economyEmail => 'Økonomi - Vektorprogrammet'))
            ->setTo($receipt->getUser()->getEmail())
            ->setBody($this->twig->render('receipt/confirmation_email.txt.twig', array(
                'name' => $receipt->getUser()->getFullName(),
                'account_number' => $receipt->getUser()->getAccountNumber(),
                'receipt' => $receipt, )));

        $this->mailer->send($message);
    }

    public function sendRejectedReceiptConfirmation(Receipt $receipt)
    {
        $message = \Swift_Message::newInstance()
                                 ->setSubject('Refusjon for utlegget ditt har blitt avvist')
                                 ->setFrom(array($this->economyEmail => 'Økonomi - Vektorprogrammet'))
                                 ->setReplyTo($this->economyEmail)
                                 ->setTo($receipt->getUser()->getEmail())
                                 ->setBody($this->twig->render('receipt/rejected_email.txt.twig', array(
                                     'name' => $receipt->getUser()->getFullName(),
                                     'receipt' => $receipt,)));

        $this->mailer->send($message);
    }

    public function sendReceiptCreatedNotification(Receipt $receipt)
    {
        $message = \Swift_Message::newInstance()
                                 ->setSubject('Nytt utlegg fra '.$receipt->getUser())
                                 ->setFrom('vektorbot@vektorprogrammet.no')
                                 ->setTo($this->economyEmail)
                                 ->setBody($this->twig->render('receipt/created_email.html.twig', array(
                                      'url' => $this->router->generate('receipts_show_individual', ['user' => $receipt->getUser()->getId()]),
                                     'name' => $receipt->getUser()->getFullName(),
                                     'accountNumber' => $receipt->getUser()->getAccountNumber(),
                                     'receipt' => $receipt, )), 'text/html')
                                 ->setContentType('text/html');

        $this->mailer->send($message);
    }

    public function sendAdmissionStartedNotification(AdmissionSubscriber $subscriber)
    {
        $message = \Swift_Message::newInstance()
             ->setSubject('Opptak for vektorassistenter har åpnet!')
             ->setFrom($this->defaultEmail)
             ->setTo($subscriber->getEmail())
             ->setBody($this->twig->render('admission/notification_email.html.twig', array(
                 'department' => $subscriber->getDepartment(),
                 'infoMeeting' => $subscriber->getDepartment()->getCurrentSemester()->getInfoMeeting(),
                 'subscriber' => $subscriber,
             )))
             ->setContentType('text/html');

        $this->mailer->send($message, true);
    }

    public function sendInfoMeetingNotification(AdmissionSubscriber $subscriber)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Infomøte i morgen!')
            ->setFrom($this->defaultEmail)
            ->setTo($subscriber->getEmail())
            ->setBody($this->twig->render('admission/info_meeting_email.html.twig', array(
                'department' => $subscriber->getDepartment(),
                'infoMeeting' => $subscriber->getDepartment()->getCurrentSemester()->getInfoMeeting(),
                'subscriber' => $subscriber,
            )))
            ->setContentType('text/html');
        $this->mailer->send($message, true);
    }

}

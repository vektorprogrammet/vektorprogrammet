<?php

namespace App\Service;

use App\Entity\AdmissionSubscriber;
use App\Operations\Infrastructure\Entity\Receipt;
use App\Content\Infrastructure\Entity\SupportTicket;
use App\Support\Infrastructure\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class EmailSender
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly Environment $twig,
        private readonly RouterInterface $router,
        private readonly string $defaultEmail,
        private readonly string $economyEmail,
    ) {
    }

    public function sendSupportTicketToDepartment(SupportTicket $supportTicket)
    {
        $message = (new Email())
            ->subject('Nytt kontaktskjema')
            ->from($this->defaultEmail)
            ->replyTo($supportTicket->getEmail())
            ->to($supportTicket->getDepartment()->getEmail())
            ->text($this->twig->render('admission/contactEmail.txt.twig', ['contact' => $supportTicket]));
        $this->mailer->send($message);
    }

    public function sendSupportTicketReceipt(SupportTicket $supportTicket)
    {
        $receipt = (new Email())
            ->subject('Kvittering for kontaktskjema')
            ->from($this->defaultEmail)
            ->replyTo($supportTicket->getDepartment()->getEmail())
            ->to($supportTicket->getEmail())
            ->text($this->twig->render('admission/receiptEmail.txt.twig', ['contact' => $supportTicket]));
        $this->mailer->send($receipt);
    }

    public function sendPaidReceiptConfirmation(Receipt $receipt)
    {
        $message = (new Email())
            ->subject('Vi har tilbakebetalt penger for utlegget ditt')
            ->from(new Address($this->economyEmail, 'Økonomi - Vektorprogrammet'))
            ->to($receipt->getUser()->getEmail())
            ->text($this->twig->render('receipt/confirmation_email.txt.twig', [
                'name' => $receipt->getUser()->getFullName(),
                'account_number' => $receipt->getUser()->getAccountNumber(),
                'receipt' => $receipt, ]));

        $this->mailer->send($message);
    }

    public function sendRejectedReceiptConfirmation(Receipt $receipt)
    {
        $message = (new Email())
                                 ->subject('Refusjon for utlegget ditt har blitt avvist')
                                 ->from(new Address($this->economyEmail, 'Økonomi - Vektorprogrammet'))
                                 ->replyTo($this->economyEmail)
                                 ->to($receipt->getUser()->getEmail())
                                 ->text($this->twig->render('receipt/rejected_email.txt.twig', [
                                     'name' => $receipt->getUser()->getFullName(),
                                     'receipt' => $receipt, ]));

        $this->mailer->send($message);
    }

    public function sendReceiptCreatedNotification(Receipt $receipt)
    {
        $message = (new Email())
                                 ->subject('Nytt utlegg fra '.$receipt->getUser())
                                 ->from('vektorbot@vektorprogrammet.no')
                                 ->to($this->economyEmail)
                                 ->html($this->twig->render('receipt/created_email.html.twig', [
                                     'url' => $this->router->generate('receipts_show_individual', ['user' => $receipt->getUser()->getId()]),
                                     'name' => $receipt->getUser()->getFullName(),
                                     'accountNumber' => $receipt->getUser()->getAccountNumber(),
                                     'receipt' => $receipt, ]));

        $this->mailer->send($message);
    }

    public function sendAdmissionStartedNotification(AdmissionSubscriber $subscriber)
    {
        $message = (new Email())
             ->subject('Opptak for vektorassistenter har åpnet!')
             ->from($this->defaultEmail)
             ->to($subscriber->getEmail())
             ->html($this->twig->render('admission/notification_email.html.twig', [
                 'department' => $subscriber->getDepartment(),
                 'infoMeeting' => $subscriber->getDepartment()->getCurrentAdmissionPeriod()->getInfoMeeting(),
                 'subscriber' => $subscriber,
             ]));

        $this->mailer->send($message, true);
    }

    public function sendInfoMeetingNotification(AdmissionSubscriber $subscriber)
    {
        $message = (new Email())
            ->subject('Infomøte i dag!')
            ->from($this->defaultEmail)
            ->to($subscriber->getEmail())
            ->html($this->twig->render('admission/info_meeting_email.html.twig', [
                'department' => $subscriber->getDepartment(),
                'infoMeeting' => $subscriber->getDepartment()->getCurrentAdmissionPeriod()->getInfoMeeting(),
                'subscriber' => $subscriber,
            ]));
        $this->mailer->send($message, true);
    }
}

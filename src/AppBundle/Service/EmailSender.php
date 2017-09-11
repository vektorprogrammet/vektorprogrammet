<?php

namespace AppBundle\Service;

use AppBundle\Entity\SupportTicket;
use AppBundle\Entity\Receipt;
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

	public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig, Router $router, LoggerInterface $logger, string $defaultEmail, string $economyEmail)
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

    public function sendPaidReceiptConfirmation(Receipt $receipt)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Vi har tilbakebetalt penger for utlegget ditt')
            ->setFrom($this->defaultEmail)
            ->setReplyTo($this->defaultEmail)
            ->setTo($receipt->getUser()->getEmail())
            ->setBody($this->twig->render('receipt/confirmation_email.txt.twig', array(
                'name' => $receipt->getUser()->getFullName(),
                'account_number' => $receipt->getUser()->getAccountNumber(),
                'receipt' => $receipt, )));

        $this->mailer->send($message);

        $this->logger->info(
            "Confirmation for paid receipt sent to {$receipt->getUser()->getFullName()} at {$receipt->getUser()->getEmail()}"
        );
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

		$this->logger->info(
			"Receipt created email sent to $this->economyEmail"
		);
	}
}

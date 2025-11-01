<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\AdmissionSubscriber;
use AppBundle\Entity\Department;
use AppBundle\Entity\InfoMeeting;
use AppBundle\Entity\Receipt;
use AppBundle\Entity\SupportTicket;
use AppBundle\Entity\User;
use AppBundle\Mailer\MailerInterface;
use AppBundle\Service\EmailSender;
use PHPUnit\Framework\TestCase;
use Swift_Message;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class EmailSenderTest extends TestCase
{
    /**
     * @var EmailSender
     */
    private $service;

    /**
     * @var MailerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $mailer;

    /**
     * @var Environment|\PHPUnit\Framework\MockObject\MockObject
     */
    private $twig;

    /**
     * @var RouterInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $router;

    protected function setUp()
    {
        $this->mailer = $this->createMock(MailerInterface::class);
        $this->twig = $this->createMock(Environment::class);
        $this->router = $this->createMock(RouterInterface::class);

        $this->service = new EmailSender(
            $this->mailer,
            $this->twig,
            $this->router,
            'default@example.com',
            'economy@example.com'
        );
    }

    public function testSendSupportTicketToDepartment()
    {
        $department = new Department();
        $department->setEmail('dept@example.com');

        $supportTicket = new SupportTicket();
        $supportTicket->setEmail('user@example.com');
        $supportTicket->setDepartment($department);

        $this->twig->expects($this->once())
            ->method('render')
            ->with('admission/contactEmail.txt.twig', ['contact' => $supportTicket])
            ->willReturn('Email body');

        $this->mailer->expects($this->once())
            ->method('send')
            ->with($this->callback(function (Swift_Message $message) {
                return $message->getSubject() === 'Nytt kontaktskjema' &&
                       $message->getTo()['dept@example.com'] !== null;
            }));

        $this->service->sendSupportTicketToDepartment($supportTicket);
    }

    public function testSendPaidReceiptConfirmation()
    {
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setFirstName('John');
        $user->setLastName('Doe');
        $user->setAccountNumber('12345678901');

        $receipt = new Receipt();
        $receipt->setUser($user);

        $this->twig->expects($this->once())
            ->method('render')
            ->with('receipt/confirmation_email.txt.twig', $this->isType('array'))
            ->willReturn('Email body');

        $this->mailer->expects($this->once())
            ->method('send')
            ->with($this->callback(function (Swift_Message $message) {
                return $message->getSubject() === 'Vi har tilbakebetalt penger for utlegget ditt';
            }));

        $this->service->sendPaidReceiptConfirmation($receipt);
    }

    public function testSendAdmissionStartedNotification()
    {
        $department = new Department();
        $admissionPeriod = new AdmissionPeriod();
        $infoMeeting = new InfoMeeting();
        $admissionPeriod->setInfoMeeting($infoMeeting);
        $department->setCurrentAdmissionPeriod($admissionPeriod);

        $subscriber = new AdmissionSubscriber();
        $subscriber->setEmail('subscriber@example.com');
        $subscriber->setDepartment($department);

        $this->twig->expects($this->once())
            ->method('render')
            ->with('admission/notification_email.html.twig', $this->isType('array'))
            ->willReturn('<html>Email body</html>');

        $this->mailer->expects($this->once())
            ->method('send')
            ->with($this->callback(function (Swift_Message $message) {
                return $message->getSubject() === 'Opptak for vektorassistenter har åpnet!';
            }), true);

        $this->service->sendAdmissionStartedNotification($subscriber);
    }
}


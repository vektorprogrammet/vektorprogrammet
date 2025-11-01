<?php

namespace Tests\AppBundle\Service;

use AppBundle\Service\SlackMailer;
use AppBundle\Service\Contract\SlackMessengerInterface;
use Nexy\Slack\Message;
use PHPUnit\Framework\TestCase;
use Swift_Message;

class SlackMailerTest extends TestCase
{
    /**
     * @var SlackMailer
     */
    private $service;

    /**
     * @var SlackMessengerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $messenger;

    protected function setUp()
    {
        $this->messenger = $this->createMock(SlackMessengerInterface::class);

        $this->service = new SlackMailer($this->messenger);
    }

    public function testSend()
    {
        $emailMessage = new Swift_Message();
        $emailMessage->setSubject('Test Subject');
        $emailMessage->setBody('Test body content');
        $emailMessage->setTo('recipient@example.com');
        $emailMessage->setFrom('sender@example.com');

        $slackMessage = $this->createMock(Message::class);

        $this->messenger->expects($this->once())
            ->method('createMessage')
            ->willReturn($slackMessage);

        $slackMessage->expects($this->once())
            ->method('setText')
            ->with('Email sent')
            ->willReturnSelf();

        $slackMessage->expects($this->once())
            ->method('setAttachments')
            ->willReturnSelf();

        $this->messenger->expects($this->once())
            ->method('send')
            ->with($slackMessage);

        $this->service->send($emailMessage);
    }

    public function testSendWithDisableLogging()
    {
        $emailMessage = new Swift_Message();
        $emailMessage->setSubject('Test Subject');
        $emailMessage->setBody('Test body content');
        $emailMessage->setTo('recipient@example.com');
        $emailMessage->setFrom('sender@example.com');

        $slackMessage = $this->createMock(Message::class);

        $this->messenger->expects($this->once())
            ->method('createMessage')
            ->willReturn($slackMessage);

        $slackMessage->expects($this->once())
            ->method('setText')
            ->with('Email sent')
            ->willReturnSelf();

        $slackMessage->expects($this->once())
            ->method('setAttachments')
            ->willReturnSelf();

        $this->messenger->expects($this->once())
            ->method('send')
            ->with($slackMessage);

        $this->service->send($emailMessage, true);
    }
}


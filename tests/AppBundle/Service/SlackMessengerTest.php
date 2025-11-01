<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\Department;
use AppBundle\Service\SlackMessenger;
use Monolog\Logger;
use Nexy\Slack\Client;
use Nexy\Slack\Message;
use PHPUnit\Framework\TestCase;

class SlackMessengerTest extends TestCase
{
    /**
     * @var SlackMessenger
     */
    private $service;

    /**
     * @var Client|\PHPUnit\Framework\MockObject\MockObject
     */
    private $slackClient;

    /**
     * @var Logger|\PHPUnit\Framework\MockObject\MockObject
     */
    private $logger;

    protected function setUp()
    {
        $this->slackClient = $this->createMock(Client::class);
        $this->logger = $this->createMock(Logger::class);

        $this->service = new SlackMessenger(
            $this->slackClient,
            '#notifications',
            '#logs',
            false,
            $this->logger
        );
    }

    public function testNotify()
    {
        $messageBody = 'Test notification message';
        $message = $this->createMock(Message::class);

        $this->slackClient->expects($this->once())
            ->method('createMessage')
            ->willReturn($message);

        $message->expects($this->once())
            ->method('to')
            ->with('#notifications')
            ->willReturnSelf();

        $message->expects($this->once())
            ->method('setText')
            ->with($messageBody)
            ->willReturnSelf();

        $this->logger->expects($this->once())
            ->method('info');

        $this->service->notify($messageBody);
    }

    public function testLog()
    {
        $messageBody = 'Test log message';
        $attachmentData = ['color' => '#ff0000'];
        $message = $this->createMock(Message::class);

        $this->slackClient->expects($this->once())
            ->method('createMessage')
            ->willReturn($message);

        $message->expects($this->once())
            ->method('to')
            ->with('#logs')
            ->willReturnSelf();

        $message->expects($this->once())
            ->method('setAttachments')
            ->willReturnSelf();

        $this->logger->expects($this->once())
            ->method('info');

        $this->service->log($messageBody, $attachmentData);
    }

    public function testMessageDepartment()
    {
        $messageBody = 'Test department message';
        $department = new Department();
        $department->setSlackChannel('#department-channel');
        $message = $this->createMock(Message::class);

        $this->slackClient->expects($this->once())
            ->method('createMessage')
            ->willReturn($message);

        $message->expects($this->once())
            ->method('to')
            ->with('#department-channel')
            ->willReturnSelf();

        $message->expects($this->once())
            ->method('setText')
            ->with($messageBody)
            ->willReturnSelf();

        $this->logger->expects($this->once())
            ->method('info');

        $this->service->messageDepartment($messageBody, $department);
    }

    public function testMessageDepartmentWithoutChannel()
    {
        $messageBody = 'Test department message';
        $department = new Department();
        $department->setSlackChannel(null);

        $this->slackClient->expects($this->never())
            ->method('createMessage');

        $this->service->messageDepartment($messageBody, $department);
    }

    public function testCreateMessage()
    {
        $message = $this->createMock(Message::class);

        $this->slackClient->expects($this->once())
            ->method('createMessage')
            ->willReturn($message);

        $result = $this->service->createMessage();

        $this->assertSame($message, $result);
    }
}


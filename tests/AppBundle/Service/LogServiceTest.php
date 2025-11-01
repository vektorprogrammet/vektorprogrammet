<?php

namespace Tests\AppBundle\Service;

use AppBundle\Service\Contract\SlackMessengerInterface;
use AppBundle\Service\Contract\UserServiceInterface;
use AppBundle\Service\LogService;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class LogServiceTest extends TestCase
{
    /**
     * @var LogService
     */
    private $service;

    /**
     * @var Logger|\PHPUnit\Framework\MockObject\MockObject
     */
    private $monoLogger;

    /**
     * @var SlackMessengerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $slackMessenger;

    /**
     * @var UserServiceInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $userService;

    /**
     * @var RequestStack|\PHPUnit\Framework\MockObject\MockObject
     */
    private $requestStack;

    protected function setUp()
    {
        $this->monoLogger = $this->createMock(Logger::class);
        $this->slackMessenger = $this->createMock(SlackMessengerInterface::class);
        $this->userService = $this->createMock(UserServiceInterface::class);
        $this->requestStack = $this->createMock(RequestStack::class);

        $this->service = new LogService(
            $this->monoLogger,
            $this->slackMessenger,
            $this->userService,
            $this->requestStack,
            'test'
        );
    }

    public function testEmergency()
    {
        $message = 'Emergency message';
        $context = ['key' => 'value'];

        $this->monoLogger->expects($this->once())
            ->method('emergency')
            ->with($message, $context);

        $request = new Request();
        $request->server->set('REQUEST_URI', '/test');
        $this->requestStack->expects($this->once())
            ->method('getMasterRequest')
            ->willReturn($request);

        $this->userService->expects($this->once())
            ->method('getCurrentUserNameAndDepartment')
            ->willReturn('Test User');

        $this->userService->expects($this->once())
            ->method('getCurrentProfilePicture')
            ->willReturn('http://example.com/pic.jpg');

        $this->slackMessenger->expects($this->once())
            ->method('log')
            ->with('', $this->isType('array'));

        $this->service->emergency($message, $context);
    }

    public function testError()
    {
        $message = 'Error message';

        $this->monoLogger->expects($this->once())
            ->method('error')
            ->with($message, []);

        $request = new Request();
        $request->server->set('REQUEST_URI', '/test');
        $this->requestStack->expects($this->once())
            ->method('getMasterRequest')
            ->willReturn($request);

        $this->userService->expects($this->once())
            ->method('getCurrentUserNameAndDepartment')
            ->willReturn('Test User');

        $this->userService->expects($this->once())
            ->method('getCurrentProfilePicture')
            ->willReturn('http://example.com/pic.jpg');

        $this->slackMessenger->expects($this->once())
            ->method('log');

        $this->service->error($message);
    }

    public function testInfo()
    {
        $message = 'Info message';

        $this->monoLogger->expects($this->once())
            ->method('info')
            ->with($message, []);

        $request = new Request();
        $request->server->set('REQUEST_URI', '/test');
        $this->requestStack->expects($this->once())
            ->method('getMasterRequest')
            ->willReturn($request);

        $this->userService->expects($this->once())
            ->method('getCurrentUserNameAndDepartment')
            ->willReturn('Test User');

        $this->userService->expects($this->once())
            ->method('getCurrentProfilePicture')
            ->willReturn('http://example.com/pic.jpg');

        $this->slackMessenger->expects($this->once())
            ->method('log');

        $this->service->info($message);
    }

    public function testDebug()
    {
        $message = 'Debug message';

        $this->monoLogger->expects($this->once())
            ->method('debug')
            ->with($message, []);

        $request = new Request();
        $request->server->set('REQUEST_URI', '/test');
        $this->requestStack->expects($this->once())
            ->method('getMasterRequest')
            ->willReturn($request);

        $this->userService->expects($this->once())
            ->method('getCurrentUserNameAndDepartment')
            ->willReturn('Test User');

        $this->userService->expects($this->once())
            ->method('getCurrentProfilePicture')
            ->willReturn('http://example.com/pic.jpg');

        $this->slackMessenger->expects($this->once())
            ->method('log');

        $this->service->debug($message);
    }
}


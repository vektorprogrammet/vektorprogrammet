<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\User;
use AppBundle\Role\Roles;
use AppBundle\Service\BetaRedirecter;
use AppBundle\Service\Contract\RoleManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class BetaRedirecterTest extends TestCase
{
    /**
     * @var BetaRedirecter
     */
    private $service;

    /**
     * @var TokenStorageInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $tokenStorage;

    /**
     * @var RoleManagerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $roleManager;

    protected function setUp()
    {
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->roleManager = $this->createMock(RoleManagerInterface::class);

        $this->service = new BetaRedirecter($this->tokenStorage, $this->roleManager);
    }

    public function testOnKernelRequestWithNonMasterRequest()
    {
        $event = $this->createMock(GetResponseEvent::class);
        $event->expects($this->once())
            ->method('isMasterRequest')
            ->willReturn(false);

        $result = $this->service->onKernelRequest($event);

        $this->assertSame($event, $result);
    }

    public function testOnKernelRequestWithoutUser()
    {
        $request = new Request();
        $request->headers->set('host', 'vektorprogrammet.no');
        $request->server->set('REQUEST_URI', '/test');

        $event = $this->createMock(GetResponseEvent::class);
        $event->expects($this->once())
            ->method('isMasterRequest')
            ->willReturn(true);
        $event->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);

        $this->tokenStorage->expects($this->once())
            ->method('getToken')
            ->willReturn(null);

        $result = $this->service->onKernelRequest($event);

        $this->assertSame($event, $result);
    }

    public function testOnKernelRequestWithTeamLeaderOnLiveServer()
    {
        $user = new User();
        $token = $this->createMock(TokenInterface::class);
        $token->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $request = new Request();
        $request->headers->set('host', 'vektorprogrammet.no');
        $request->server->set('REQUEST_URI', '/test');
        $request->server->set('HTTP_HOST', 'vektorprogrammet.no');

        $event = $this->createMock(GetResponseEvent::class);
        $event->expects($this->once())
            ->method('isMasterRequest')
            ->willReturn(true);
        $event->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);

        $this->tokenStorage->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        $this->roleManager->expects($this->once())
            ->method('userIsGranted')
            ->with($user, Roles::TEAM_LEADER)
            ->willReturn(true);

        $event->expects($this->once())
            ->method('setResponse')
            ->with($this->callback(function (RedirectResponse $response) {
                return strpos($response->getTargetUrl(), 'beta.vektorprogrammet.no') !== false;
            }));

        $result = $this->service->onKernelRequest($event);

        $this->assertSame($event, $result);
    }

    public function testOnKernelRequestWithNonTeamLeader()
    {
        $user = new User();
        $token = $this->createMock(TokenInterface::class);
        $token->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $request = new Request();
        $request->headers->set('host', 'vektorprogrammet.no');

        $event = $this->createMock(GetResponseEvent::class);
        $event->expects($this->once())
            ->method('isMasterRequest')
            ->willReturn(true);
        $event->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);

        $this->tokenStorage->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        $this->roleManager->expects($this->once())
            ->method('userIsGranted')
            ->with($user, Roles::TEAM_LEADER)
            ->willReturn(false);

        $event->expects($this->never())
            ->method('setResponse');

        $result = $this->service->onKernelRequest($event);

        $this->assertSame($event, $result);
    }
}


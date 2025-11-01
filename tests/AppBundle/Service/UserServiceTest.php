<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\User;
use AppBundle\Service\UserService;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    /**
     * @var UserService
     */
    private $service;

    /**
     * @var TokenStorageInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $tokenStorage;

    protected function setUp()
    {
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->service = new UserService($this->tokenStorage);
    }

    public function testGetCurrentUserReturnsNullWhenNoToken()
    {
        $this->tokenStorage->expects($this->once())
            ->method('getToken')
            ->willReturn(null);

        $result = $this->service->getCurrentUser();

        $this->assertNull($result);
    }

    public function testGetCurrentUserReturnsNullWhenNotUserInstance()
    {
        $token = $this->createMock(TokenInterface::class);
        $token->expects($this->once())
            ->method('getUser')
            ->willReturn('anonymous');

        $this->tokenStorage->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        $result = $this->service->getCurrentUser();

        $this->assertNull($result);
    }

    public function testGetCurrentUserReturnsUser()
    {
        $user = new User();
        $user->setEmail('test@example.com');

        $token = $this->createMock(TokenInterface::class);
        $token->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $this->tokenStorage->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        $result = $this->service->getCurrentUser();

        $this->assertSame($user, $result);
    }

    public function testGetCurrentUserNameReturnsAnonymousWhenNoUser()
    {
        $this->tokenStorage->expects($this->once())
            ->method('getToken')
            ->willReturn(null);

        $result = $this->service->getCurrentUserName();

        $this->assertEquals('Anonymous', $result);
    }

    public function testGetCurrentUserNameReturnsUserName()
    {
        $user = new User();
        $user->setFirstName('John');
        $user->setLastName('Doe');

        $token = $this->createMock(TokenInterface::class);
        $token->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $this->tokenStorage->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        $result = $this->service->getCurrentUserName();

        $this->assertNotEmpty($result);
        $this->assertNotEquals('Anonymous', $result);
    }

    public function testGetCurrentUserNameAndDepartmentReturnsAnonymousWhenNoUser()
    {
        $this->tokenStorage->expects($this->once())
            ->method('getToken')
            ->willReturn(null);

        $result = $this->service->getCurrentUserNameAndDepartment();

        $this->assertEquals('Anonymous', $result);
    }

    public function testGetCurrentProfilePictureReturnsDefaultWhenNoUser()
    {
        $this->tokenStorage->expects($this->once())
            ->method('getToken')
            ->willReturn(null);

        $result = $this->service->getCurrentProfilePicture();

        $this->assertStringContainsString('defaultProfile.png', $result);
    }

    public function testGetCurrentProfilePictureReturnsUserPicture()
    {
        $user = new User();
        $user->setPicturePath('uploads/user.jpg');

        $token = $this->createMock(TokenInterface::class);
        $token->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $this->tokenStorage->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        $result = $this->service->getCurrentProfilePicture();

        $this->assertStringContainsString('user.jpg', $result);
    }
}


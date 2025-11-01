<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\PasswordReset;
use AppBundle\Entity\User;
use AppBundle\Mailer\MailerInterface;
use AppBundle\Service\PasswordManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

class PasswordManagerTest extends TestCase
{
    /**
     * @var PasswordManager
     */
    private $service;

    /**
     * @var EntityManagerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $em;

    /**
     * @var MailerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $mailer;

    /**
     * @var Environment|\PHPUnit\Framework\MockObject\MockObject
     */
    private $twig;

    protected function setUp()
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->mailer = $this->createMock(MailerInterface::class);
        $this->twig = $this->createMock(Environment::class);

        $this->service = new PasswordManager($this->em, $this->mailer, $this->twig);
    }

    public function testGenerateRandomResetCode()
    {
        $code = $this->service->generateRandomResetCode();

        $this->assertNotEmpty($code);
        $this->assertEquals(24, strlen($code)); // 12 bytes = 24 hex characters
    }

    public function testHashCode()
    {
        $resetCode = 'test-reset-code';
        $hashed = $this->service->hashCode($resetCode);

        $this->assertNotEmpty($hashed);
        $this->assertEquals(128, strlen($hashed)); // SHA512 produces 128 hex characters
    }

    public function testHashCodeIsDeterministic()
    {
        $resetCode = 'test-reset-code';
        $hashed1 = $this->service->hashCode($resetCode);
        $hashed2 = $this->service->hashCode($resetCode);

        $this->assertEquals($hashed1, $hashed2);
    }

    public function testResetCodeIsValidReturnsTrueWhenValid()
    {
        $resetCode = 'test-code';
        $hashedCode = $this->service->hashCode($resetCode);

        $user = new User();
        $passwordReset = new PasswordReset();
        $passwordReset->setUser($user);

        $repo = $this->createMock(EntityRepository::class);
        $repo->expects($this->once())
            ->method('findPasswordResetByHashedResetCode')
            ->with($hashedCode)
            ->willReturn($passwordReset);

        $this->em->expects($this->once())
            ->method('getRepository')
            ->with(PasswordReset::class)
            ->willReturn($repo);

        $result = $this->service->resetCodeIsValid($resetCode);

        $this->assertTrue($result);
    }

    public function testResetCodeIsValidReturnsFalseWhenNotFound()
    {
        $resetCode = 'test-code';
        $hashedCode = $this->service->hashCode($resetCode);

        $repo = $this->createMock(EntityRepository::class);
        $repo->expects($this->once())
            ->method('findPasswordResetByHashedResetCode')
            ->with($hashedCode)
            ->willReturn(null);

        $this->em->expects($this->once())
            ->method('getRepository')
            ->with(PasswordReset::class)
            ->willReturn($repo);

        $result = $this->service->resetCodeIsValid($resetCode);

        $this->assertFalse($result);
    }

    public function testResetCodeIsValidReturnsFalseWhenNoUser()
    {
        $resetCode = 'test-code';
        $hashedCode = $this->service->hashCode($resetCode);

        $passwordReset = new PasswordReset();
        $passwordReset->setUser(null);

        $repo = $this->createMock(EntityRepository::class);
        $repo->expects($this->once())
            ->method('findPasswordResetByHashedResetCode')
            ->with($hashedCode)
            ->willReturn($passwordReset);

        $this->em->expects($this->once())
            ->method('getRepository')
            ->with(PasswordReset::class)
            ->willReturn($repo);

        $result = $this->service->resetCodeIsValid($resetCode);

        $this->assertFalse($result);
    }

    public function testCreatePasswordResetEntityReturnsNullWhenUserNotFound()
    {
        $email = 'nonexistent@example.com';

        $repo = $this->createMock(EntityRepository::class);
        $repo->expects($this->once())
            ->method('findUserByEmail')
            ->with($email)
            ->willReturn(null);

        $this->em->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($repo);

        $result = $this->service->createPasswordResetEntity($email);

        $this->assertNull($result);
    }

    public function testCreatePasswordResetEntityReturnsPasswordReset()
    {
        $email = 'test@example.com';
        $user = new User();
        $user->setEmail($email);

        $repo = $this->createMock(EntityRepository::class);
        $repo->expects($this->once())
            ->method('findUserByEmail')
            ->with($email)
            ->willReturn($user);

        $this->em->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($repo);

        $result = $this->service->createPasswordResetEntity($email);

        $this->assertInstanceOf(PasswordReset::class, $result);
        $this->assertSame($user, $result->getUser());
        $this->assertNotEmpty($result->getResetCode());
        $this->assertNotEmpty($result->getHashedResetCode());
    }
}


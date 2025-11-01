<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\Department;
use AppBundle\Entity\FieldOfStudy;
use AppBundle\Entity\User;
use AppBundle\Mailer\MailerInterface;
use AppBundle\Service\UserRegistration;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Swift_Message;
use Twig\Environment;

class UserRegistrationTest extends TestCase
{
    /**
     * @var UserRegistration
     */
    private $service;

    /**
     * @var Environment|\PHPUnit\Framework\MockObject\MockObject
     */
    private $twig;

    /**
     * @var EntityManagerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $em;

    /**
     * @var MailerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $mailer;

    protected function setUp()
    {
        $this->twig = $this->createMock(Environment::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->mailer = $this->createMock(MailerInterface::class);

        $this->service = new UserRegistration($this->twig, $this->em, $this->mailer);
    }

    public function testSetNewUserCode()
    {
        $user = new User();

        $this->em->expects($this->once())
            ->method('persist')
            ->with($user);

        $this->em->expects($this->once())
            ->method('flush');

        $code = $this->service->setNewUserCode($user);

        $this->assertNotEmpty($code);
        $this->assertEquals(32, strlen($code)); // 16 bytes = 32 hex characters
        $this->assertNotEmpty($user->getNewUserCode());
    }

    public function testGetHashedCode()
    {
        $code = 'test-code-123';
        $hashed = $this->service->getHashedCode($code);

        $this->assertNotEmpty($hashed);
        $this->assertEquals(128, strlen($hashed)); // SHA512 produces 128 hex characters
    }

    public function testGetHashedCodeIsDeterministic()
    {
        $code = 'test-code-123';
        $hashed1 = $this->service->getHashedCode($code);
        $hashed2 = $this->service->getHashedCode($code);

        $this->assertEquals($hashed1, $hashed2);
    }

    public function testCreateActivationEmail()
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setFirstName('John');
        $user->setLastName('Doe');

        $fieldOfStudy = new FieldOfStudy();
        $department = new Department();
        $department->setEmail('dept@example.com');
        $fieldOfStudy->setDepartment($department);
        $user->setFieldOfStudy($fieldOfStudy);

        $code = 'activation-code-123';

        $this->twig->expects($this->once())
            ->method('render')
            ->with('new_user/create_new_user_email.txt.twig', [
                'newUserCode' => $code,
                'name' => $user->getFullName(),
            ])
            ->willReturn('Email body content');

        $message = $this->service->createActivationEmail($user, $code);

        $this->assertInstanceOf(Swift_Message::class, $message);
        $this->assertEquals('Velkommen til Vektorprogrammet!', $message->getSubject());
    }

    public function testSendActivationCode()
    {
        $user = new User();
        $user->setEmail('test@example.com');

        $fieldOfStudy = new FieldOfStudy();
        $department = new Department();
        $department->setEmail('dept@example.com');
        $fieldOfStudy->setDepartment($department);
        $user->setFieldOfStudy($fieldOfStudy);

        $this->em->expects($this->once())
            ->method('persist')
            ->with($user);

        $this->em->expects($this->once())
            ->method('flush');

        $this->twig->expects($this->once())
            ->method('render')
            ->willReturn('Email body');

        $this->mailer->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(Swift_Message::class));

        $this->service->sendActivationCode($user);
    }
}


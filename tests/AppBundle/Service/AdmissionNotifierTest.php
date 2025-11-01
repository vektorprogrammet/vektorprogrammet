<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\AdmissionSubscriber;
use AppBundle\Entity\Department;
use AppBundle\Service\AdmissionNotifier;
use AppBundle\Service\Contract\EmailSenderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AdmissionNotifierTest extends TestCase
{
    /**
     * @var AdmissionNotifier
     */
    private $service;

    /**
     * @var EntityManagerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $em;

    protected function setUp()
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $emailSender = $this->createMock(EmailSenderInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);

        $this->service = new AdmissionNotifier($this->em, $emailSender, $logger, $validator, 100);
    }

    public function testCreateSubscription()
    {
        $department = new Department();
        $email = 'test@example.com';

        $repo = $this->createMock(EntityRepository::class);
        $repo->expects($this->once())
            ->method('findByEmailAndDepartment')
            ->with($email, $department)
            ->willReturn(null);

        $this->em->expects($this->once())
            ->method('getRepository')
            ->with(AdmissionSubscriber::class)
            ->willReturn($repo);

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList());

        // Create service with the validator mock
        $emailSender = $this->createMock(EmailSenderInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $service = new AdmissionNotifier($this->em, $emailSender, $logger, $validator, 100);

        $this->em->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(AdmissionSubscriber::class));

        $this->em->expects($this->once())
            ->method('flush');

        $service->createSubscription($department, $email);
    }

    public function testCreateSubscriptionWithExistingSubscription()
    {
        $department = new Department();
        $email = 'test@example.com';

        $existingSubscriber = new AdmissionSubscriber();

        $repo = $this->createMock(EntityRepository::class);
        $repo->expects($this->once())
            ->method('findByEmailAndDepartment')
            ->with($email, $department)
            ->willReturn($existingSubscriber);

        $this->em->expects($this->once())
            ->method('getRepository')
            ->with(AdmissionSubscriber::class)
            ->willReturn($repo);

        $validator = $this->createMock(ValidatorInterface::class);
        $emailSender = $this->createMock(EmailSenderInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $service = new AdmissionNotifier($this->em, $emailSender, $logger, $validator, 100);

        $this->em->expects($this->never())
            ->method('persist');

        $service->createSubscription($department, $email);
    }
}


<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\Application;
use AppBundle\Entity\Department;
use AppBundle\Entity\Repository\ApplicationRepository;
use AppBundle\Entity\User;
use AppBundle\Service\ApplicationData;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ApplicationDataTest extends TestCase
{
    /**
     * @var ApplicationData
     */
    private $service;

    /**
     * @var EntityManagerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $em;

    /**
     * @var ApplicationRepository|\PHPUnit\Framework\MockObject\MockObject
     */
    private $repository;

    protected function setUp()
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $token = $this->createMock(TokenInterface::class);

        $user = new User();
        $department = new Department();
        $user->setDepartment($department);

        $token->expects($this->any())
            ->method('getUser')
            ->willReturn($user);

        $tokenStorage->expects($this->any())
            ->method('getToken')
            ->willReturn($token);

        $this->repository = $this->createMock(ApplicationRepository::class);

        $this->em->expects($this->once())
            ->method('getRepository')
            ->with(Application::class)
            ->willReturn($this->repository);

        $this->service = new ApplicationData($this->em, $tokenStorage);
    }

    public function testGetApplicationCount()
    {
        $admissionPeriod = new AdmissionPeriod();
        $this->service->setAdmissionPeriod($admissionPeriod);

        $this->repository->expects($this->once())
            ->method('numOfApplications')
            ->with($admissionPeriod)
            ->willReturn(10);

        $result = $this->service->getApplicationCount();

        $this->assertEquals(10, $result);
    }

    public function testGetApplicationCountWithoutAdmissionPeriod()
    {
        $result = $this->service->getApplicationCount();

        $this->assertEquals(0, $result);
    }

    public function testGetMaleCount()
    {
        $admissionPeriod = new AdmissionPeriod();
        $this->service->setAdmissionPeriod($admissionPeriod);

        $this->repository->expects($this->once())
            ->method('numOfGender')
            ->with($admissionPeriod, 0)
            ->willReturn(5);

        $result = $this->service->getMaleCount();

        $this->assertEquals(5, $result);
    }

    public function testGetFemaleCount()
    {
        $admissionPeriod = new AdmissionPeriod();
        $this->service->setAdmissionPeriod($admissionPeriod);

        $this->repository->expects($this->once())
            ->method('numOfGender')
            ->with($admissionPeriod, 1)
            ->willReturn(5);

        $result = $this->service->getFemaleCount();

        $this->assertEquals(5, $result);
    }

    public function testGetMalePercentage()
    {
        $admissionPeriod = new AdmissionPeriod();
        $this->service->setAdmissionPeriod($admissionPeriod);

        $this->repository->expects($this->once())
            ->method('numOfApplications')
            ->willReturn(10);

        $this->repository->expects($this->once())
            ->method('numOfGender')
            ->with($admissionPeriod, 0)
            ->willReturn(6);

        $result = $this->service->getMalePercentage();

        $this->assertEquals(60.0, $result);
    }

    public function testSetDepartment()
    {
        $department = new Department();
        $admissionPeriod = new AdmissionPeriod();
        $department->setCurrentAdmissionPeriod($admissionPeriod);

        $this->service->setDepartment($department);

        // Verify admission period was set
        $this->service->setAdmissionPeriod($admissionPeriod);
        $this->assertEquals(0, $this->service->getApplicationCount());
    }
}


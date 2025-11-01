<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\Semester;
use AppBundle\Service\SbsData;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SbsDataTest extends TestCase
{
    /**
     * @var SbsData
     */
    private $service;

    protected function setUp()
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        
        // SbsData extends ApplicationData, so we need to set it up similarly
        $this->service = $this->getMockBuilder(SbsData::class)
            ->setConstructorArgs([$em, $tokenStorage])
            ->onlyMethods(['getAdmissionPeriod', 'getInterviewedAssistantsCount', 'getAssignedInterviewsCount', 'getTotalAssistantsCount', 'getApplicationCount'])
            ->getMock();
    }

    public function testGetTotalApplicationsCount()
    {
        $this->service->expects($this->once())
            ->method('getApplicationCount')
            ->willReturn(10);

        $result = $this->service->getTotalApplicationsCount();

        $this->assertEquals(10, $result);
    }

    public function testGetStep()
    {
        $this->service->expects($this->once())
            ->method('getStepProgress')
            ->willReturn(3.5);

        $result = $this->service->getStep();

        $this->assertEquals(3, $result);
    }

    public function testGetAdmissionTimeLeft()
    {
        $admissionPeriod = new AdmissionPeriod();
        $endDate = new DateTime('+2 days');
        $admissionPeriod->setEndDate($endDate);

        $this->service->expects($this->once())
            ->method('getAdmissionPeriod')
            ->willReturn($admissionPeriod);

        $result = $this->service->getAdmissionTimeLeft();

        $this->assertIsInt($result);
        $this->assertGreaterThan(0, $result);
    }

    public function testGetTimeToAdmissionStart()
    {
        $admissionPeriod = new AdmissionPeriod();
        $startDate = new DateTime('+1 day');
        $admissionPeriod->setStartDate($startDate);

        $this->service->expects($this->once())
            ->method('getAdmissionPeriod')
            ->willReturn($admissionPeriod);

        $result = $this->service->getTimeToAdmissionStart();

        $this->assertIsInt($result);
    }
}


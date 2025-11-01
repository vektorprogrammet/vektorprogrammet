<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\AssistantHistory;
use AppBundle\Entity\School;
use AppBundle\Entity\User;
use AppBundle\Repository\Contract\AssistantHistoryRepositoryInterface;
use AppBundle\Service\PartnerService;
use PHPUnit\Framework\TestCase;

class PartnerServiceTest extends TestCase
{
    /**
     * @var PartnerService
     */
    private $service;

    /**
     * @var AssistantHistoryRepositoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $repository;

    protected function setUp()
    {
        $this->repository = $this->createMock(AssistantHistoryRepositoryInterface::class);
        $this->service = new PartnerService($this->repository);
    }

    public function testFindPartnersForUser()
    {
        $user = new User();
        $school = new School();
        $school->setName('Test School');

        $activeHistory = new AssistantHistory();
        $activeHistory->setUser($user);
        $activeHistory->setSchool($school);
        $activeHistory->setDay(1);

        $partnerHistory = new AssistantHistory();
        $partnerHistory->setUser(new User());
        $partnerHistory->setSchool($school);
        $partnerHistory->setDay(1);

        $this->repository->expects($this->once())
            ->method('findActiveAssistantHistoriesByUser')
            ->with($user)
            ->willReturn([$activeHistory]);

        $this->repository->expects($this->once())
            ->method('findActiveAssistantHistoriesBySchool')
            ->with($school)
            ->willReturn([$activeHistory, $partnerHistory]);

        $result = $this->service->findPartnersForUser($user);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('partnerInformations', $result);
        $this->assertArrayHasKey('partnerCount', $result);
        $this->assertGreaterThanOrEqual(0, $result['partnerCount']);
    }
}


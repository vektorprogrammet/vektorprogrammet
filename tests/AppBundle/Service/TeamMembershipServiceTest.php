<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\Semester;
use AppBundle\Entity\TeamMembership;
use AppBundle\Event\TeamMembershipEvent;
use AppBundle\Service\TeamMembershipService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TeamMembershipServiceTest extends TestCase
{
    /**
     * @var TeamMembershipService
     */
    private $service;

    /**
     * @var EntityManagerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $em;

    /**
     * @var EventDispatcherInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $dispatcher;

    protected function setUp()
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);

        $this->service = new TeamMembershipService($this->em, $this->dispatcher);
    }

    public function testUpdateTeamMemberships()
    {
        $currentSemester = new Semester();
        $currentSemester->setStartDate(new \DateTime('2024-01-01'));

        $teamMembership1 = new TeamMembership();
        $teamMembership1->setIsSuspended(false);

        $teamMembership2 = new TeamMembership();
        $teamMembership2->setIsSuspended(false);
        $endSemester = new Semester();
        $endSemester->setEndDate(new \DateTime('2023-12-31'));
        $teamMembership2->setEndSemester($endSemester);

        $memberships = [$teamMembership1, $teamMembership2];

        $membershipRepo = $this->createMock(EntityRepository::class);
        $membershipRepo->expects($this->once())
            ->method('findBy')
            ->with(['isSuspended' => false])
            ->willReturn($memberships);

        $semesterRepo = $this->createMock(EntityRepository::class);
        $semesterRepo->expects($this->once())
            ->method('findOrCreateCurrentSemester')
            ->willReturn($currentSemester);

        $this->em->expects($this->exactly(2))
            ->method('getRepository')
            ->willReturnCallback(function ($class) use ($membershipRepo, $semesterRepo) {
                if ($class === TeamMembership::class) {
                    return $membershipRepo;
                }
                if ($class === Semester::class) {
                    return $semesterRepo;
                }
                return null;
            });

        $this->dispatcher->expects($this->once())
            ->method('dispatch')
            ->with(TeamMembershipEvent::EXPIRED, $this->isInstanceOf(TeamMembershipEvent::class));

        $this->em->expects($this->once())
            ->method('flush');

        $result = $this->service->updateTeamMemberships();

        $this->assertEquals($memberships, $result);
        $this->assertTrue($teamMembership2->getIsSuspended());
    }
}


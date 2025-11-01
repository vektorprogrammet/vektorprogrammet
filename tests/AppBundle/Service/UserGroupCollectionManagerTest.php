<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\Semester;
use AppBundle\Entity\Team;
use AppBundle\Entity\TeamMembership;
use AppBundle\Entity\User;
use AppBundle\Entity\UserGroup;
use AppBundle\Entity\UserGroupCollection;
use AppBundle\Service\UserGroupCollectionManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

class UserGroupCollectionManagerTest extends TestCase
{
    /**
     * @var UserGroupCollectionManager
     */
    private $service;

    /**
     * @var EntityManagerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $em;

    protected function setUp()
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->service = new UserGroupCollectionManager($this->em);
    }

    public function testInitializeUserGroupCollection()
    {
        $collection = new UserGroupCollection();
        $collection->setNumberUserGroups(2);

        $team = new Team();
        $semester = new Semester();

        $user1 = new User();
        $user2 = new User();
        $user3 = new User();
        $user4 = new User();

        $teamMembership1 = new TeamMembership();
        $teamMembership1->setTeam($team);
        $teamMembership1->setUser($user1);

        $teamMembership2 = new TeamMembership();
        $teamMembership2->setTeam($team);
        $teamMembership2->setUser($user2);

        $teamMembership3 = new TeamMembership();
        $teamMembership3->setTeam($team);
        $teamMembership3->setUser($user3);

        $teamMembership4 = new TeamMembership();
        $teamMembership4->setTeam($team);
        $teamMembership4->setUser($user4);

        $collection->addTeam($team);
        $collection->addSemester($semester);

        $teamMembershipRepo = $this->createMock(EntityRepository::class);
        $teamMembershipRepo->expects($this->once())
            ->method('findByTeam')
            ->with($team)
            ->willReturn([$teamMembership1, $teamMembership2, $teamMembership3, $teamMembership4]);

        $teamMembershipRepo->expects($this->once())
            ->method('filterNotInSemester')
            ->willReturn([$teamMembership1, $teamMembership2, $teamMembership3, $teamMembership4]);

        $this->em->expects($this->once())
            ->method('getRepository')
            ->with(TeamMembership::class)
            ->willReturn($teamMembershipRepo);

        $this->em->expects($this->exactly(3))
            ->method('persist');

        $this->em->expects($this->once())
            ->method('flush');

        $this->service->initializeUserGroupCollection($collection);

        $this->assertEquals(4, $collection->getNumberTotalUsers());
    }

    public function testInitializeUserGroupCollectionThrowsExceptionForInvalidGroupCount()
    {
        $collection = new UserGroupCollection();
        $collection->setNumberUserGroups(0);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Ugyldig antall grupper');

        $this->service->initializeUserGroupCollection($collection);
    }
}


<?php

namespace App\Tests\App\Organization\Api\State;

use ApiPlatform\Metadata\Post;
use App\Identity\Infrastructure\AccessControlService;
use App\Identity\Infrastructure\Entity\User;
use App\Organization\Api\Resource\AdminTeamMemberInput;
use App\Organization\Api\State\AdminTeamMemberAddProcessor;
use App\Organization\Infrastructure\Entity\Position;
use App\Organization\Infrastructure\Entity\Team;
use App\Shared\Entity\Semester;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class AdminTeamMemberAddProcessorTest extends TestCase
{
    public function testMissingMedlemPositionThrowsUnprocessableEntity(): void
    {
        $team = $this->createMock(Team::class);
        $team->method('getDepartment')->willReturn(null);

        $user = $this->createMock(User::class);

        $semester = $this->createMock(Semester::class);

        $teamRepo = $this->createMock(EntityRepository::class);
        $teamRepo->method('find')->willReturn($team);

        $userRepo = $this->createMock(EntityRepository::class);
        $userRepo->method('find')->willReturn($user);

        $semesterRepo = $this->createMock(EntityRepository::class);
        $semesterRepo->method('find')->willReturn($semester);

        $positionRepo = $this->createMock(EntityRepository::class);
        $positionRepo->method('find')->willReturn(null);
        // 'Medlem' not found
        $positionRepo->method('findOneBy')->willReturn(null);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturnMap([
            [Team::class, $teamRepo],
            [User::class, $userRepo],
            [Semester::class, $semesterRepo],
            [Position::class, $positionRepo],
        ]);

        $accessControl = $this->createMock(AccessControlService::class);
        $security = $this->createMock(Security::class);
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $logger = new NullLogger();

        $processor = new AdminTeamMemberAddProcessor($accessControl, $security, $em, $dispatcher, $logger);

        $input = new AdminTeamMemberInput();
        $input->userId = 1;
        $input->startSemesterId = 1;
        $input->positionId = null;

        $this->expectException(UnprocessableEntityHttpException::class);

        $processor->process($input, new Post(), ['id' => 1]);
    }

    public function testFoundMedlemPositionDoesNotThrow(): void
    {
        $team = $this->createMock(Team::class);
        $team->method('getDepartment')->willReturn(null);

        $user = $this->createMock(User::class);
        $semester = $this->createMock(Semester::class);
        $position = $this->createMock(Position::class);

        $teamRepo = $this->createMock(EntityRepository::class);
        $teamRepo->method('find')->willReturn($team);

        $userRepo = $this->createMock(EntityRepository::class);
        $userRepo->method('find')->willReturn($user);

        $semesterRepo = $this->createMock(EntityRepository::class);
        $semesterRepo->method('find')->willReturn($semester);

        $positionRepo = $this->createMock(EntityRepository::class);
        $positionRepo->method('find')->willReturn(null);
        $positionRepo->method('findOneBy')->willReturn($position);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturnMap([
            [Team::class, $teamRepo],
            [User::class, $userRepo],
            [Semester::class, $semesterRepo],
            [Position::class, $positionRepo],
        ]);
        $em->method('persist');
        $em->method('flush');

        $accessControl = $this->createMock(AccessControlService::class);
        $security = $this->createMock(Security::class);
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->method('dispatch')->willReturn(new \stdClass());
        $logger = new NullLogger();

        $processor = new AdminTeamMemberAddProcessor($accessControl, $security, $em, $dispatcher, $logger);

        $input = new AdminTeamMemberInput();
        $input->userId = 1;
        $input->startSemesterId = 1;
        $input->positionId = null;

        // Should not throw
        $result = $processor->process($input, new Post(), ['id' => 1]);
        $this->assertIsArray($result);
    }
}

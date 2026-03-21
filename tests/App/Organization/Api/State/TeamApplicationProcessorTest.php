<?php

namespace App\Tests\App\Organization\Api\State;

use ApiPlatform\Metadata\Post;
use App\Organization\Api\Resource\TeamApplicationInput;
use App\Organization\Api\State\TeamApplicationProcessor;
use App\Organization\Domain\Events\TeamApplicationCreatedEvent;
use App\Organization\Infrastructure\Entity\Team;
use App\Organization\Infrastructure\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class TeamApplicationProcessorTest extends TestCase
{
    public function testTeamApplicationDispatchesCreatedEvent(): void
    {
        $team = $this->createMock(Team::class);

        $teamRepo = $this->createMock(TeamRepository::class);
        $teamRepo->method('find')->willReturn($team);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->once())->method('persist');
        $em->expects($this->once())->method('flush');

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with(
                $this->isInstanceOf(TeamApplicationCreatedEvent::class),
                TeamApplicationCreatedEvent::NAME
            );

        $processor = new TeamApplicationProcessor($teamRepo, $em, $dispatcher);

        $input = new TeamApplicationInput();
        $input->teamId = 1;
        $input->name = 'Kari Nordmann';
        $input->email = 'kari@example.com';
        $input->phone = '12345678';
        $input->fieldOfStudy = 'Informatikk';
        $input->yearOfStudy = 3;
        $input->motivationText = 'I want to help students.';
        $input->biography = 'I am a student.';

        $processor->process($input, new Post());
    }
}

<?php

namespace App\Tests\App\Interview\Api\State;

use ApiPlatform\Metadata\Put;
use App\Interview\Api\Resource\InterviewConductInput;
use App\Interview\Api\State\InterviewConductProcessor;
use App\Interview\Infrastructure\Entity\Interview;
use App\Interview\Infrastructure\InterviewManager;
use App\Identity\Infrastructure\Entity\User;
use App\Identity\Infrastructure\AccessControlService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class InterviewConductProcessorNullScoreTest extends TestCase
{
    public function testConductWithNoScoreDoesNotThrow(): void
    {
        $interview = $this->createMock(Interview::class);
        $interview->method('getInterviewScore')->willReturn(null);
        $interview->method('getInterviewAnswers')->willReturn([]);
        $interview->method('getApplication')->willReturn(null);

        /** @var EntityRepository<Interview>&\PHPUnit\Framework\MockObject\MockObject $repo */
        $repo = $this->createMock(EntityRepository::class);
        $repo->method('find')->willReturn($interview);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturn($repo);

        $interviewManager = $this->createMock(InterviewManager::class);
        $interviewManager->method('loggedInUserCanSeeInterview')->willReturn(true);

        $security = $this->createMock(Security::class);
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(99);
        $security->method('getUser')->willReturn($user);

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $accessControl = $this->createMock(AccessControlService::class);

        $processor = new InterviewConductProcessor($em, $interviewManager, $dispatcher, $security, $accessControl);

        $input = new InterviewConductInput();
        $input->answers = [];
        $input->interviewScore = [];

        // Must not throw
        $processor->process($input, new Put(), ['id' => 1]);
        $this->assertTrue(true);
    }
}

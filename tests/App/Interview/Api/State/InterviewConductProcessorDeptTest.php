<?php

namespace App\Tests\App\Interview\Api\State;

use ApiPlatform\Metadata\Put;
use App\Admission\Infrastructure\Entity\Application;
use App\Identity\Infrastructure\AccessControlService;
use App\Identity\Infrastructure\Entity\User;
use App\Interview\Api\Resource\InterviewConductInput;
use App\Interview\Api\State\InterviewConductProcessor;
use App\Interview\Infrastructure\Entity\Interview;
use App\Interview\Infrastructure\InterviewManager;
use App\Organization\Infrastructure\Entity\Department;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class InterviewConductProcessorDeptTest extends TestCase
{
    public function testConductDeniedWhenUserFromDifferentDepartment(): void
    {
        $this->expectException(AccessDeniedHttpException::class);

        $deptA = $this->createMock(Department::class);
        $deptA->method('getId')->willReturn(1);

        $deptB = $this->createMock(Department::class);
        $deptB->method('getId')->willReturn(2);

        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(10);
        $user->method('getDepartment')->willReturn($deptA);

        $application = $this->createMock(Application::class);
        $application->method('getDepartment')->willReturn($deptB);
        $application->method('getUser')->willReturn(null);

        $interview = $this->createMock(Interview::class);
        $interview->method('getInterviewScore')->willReturn(null);
        $interview->method('getInterviewAnswers')->willReturn([]);
        $interview->method('getApplication')->willReturn($application);

        /** @var EntityRepository<Interview>&\PHPUnit\Framework\MockObject\MockObject $repo */
        $repo = $this->createMock(EntityRepository::class);
        $repo->method('find')->willReturn($interview);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturn($repo);

        $interviewManager = $this->createMock(InterviewManager::class);
        $interviewManager->method('loggedInUserCanSeeInterview')->willReturn(true);

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($user);
        $security->method('isGranted')->willReturn(false);

        $dispatcher = $this->createMock(EventDispatcherInterface::class);

        $accessControl = $this->createMock(AccessControlService::class);
        $accessControl->expects($this->once())
            ->method('assertDepartmentAccess')
            ->with($deptB, $user)
            ->willThrowException(new AccessDeniedHttpException('You do not have access to this department.'));

        $processor = new InterviewConductProcessor($em, $interviewManager, $dispatcher, $security, $accessControl);

        $input = new InterviewConductInput();
        $input->answers = [];
        $input->interviewScore = [];

        $processor->process($input, new Put(), ['id' => 1]);
    }
}

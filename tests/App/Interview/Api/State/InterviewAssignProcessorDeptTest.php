<?php

namespace App\Tests\App\Interview\Api\State;

use ApiPlatform\Metadata\Post;
use App\Admission\Infrastructure\Entity\Application;
use App\Identity\Infrastructure\AccessControlService;
use App\Identity\Infrastructure\Entity\User;
use App\Interview\Api\Resource\InterviewAssignInput;
use App\Interview\Api\State\InterviewAssignProcessor;
use App\Interview\Infrastructure\InterviewManager;
use App\Organization\Infrastructure\Entity\Department;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class InterviewAssignProcessorDeptTest extends TestCase
{
    public function testAssignDeniedWhenUserFromDifferentDepartment(): void
    {
        $this->expectException(AccessDeniedHttpException::class);

        $deptA = $this->createMock(Department::class);
        $deptA->method('getId')->willReturn(1);

        $deptB = $this->createMock(Department::class);
        $deptB->method('getId')->willReturn(2);

        $user = $this->createMock(User::class);
        $user->method('getDepartment')->willReturn($deptA);

        $application = $this->createMock(Application::class);
        $application->method('getDepartment')->willReturn($deptB);

        /** @var EntityRepository<Application>&\PHPUnit\Framework\MockObject\MockObject $appRepo */
        $appRepo = $this->createMock(EntityRepository::class);
        $appRepo->method('find')->willReturn($application);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->willReturn($appRepo);

        $interviewManager = $this->createMock(InterviewManager::class);

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($user);

        $accessControl = $this->createMock(AccessControlService::class);
        $accessControl->expects($this->once())
            ->method('assertDepartmentAccess')
            ->with($deptB, $user)
            ->willThrowException(new AccessDeniedHttpException('You do not have access to this department.'));

        $processor = new InterviewAssignProcessor($em, $interviewManager, $security, $accessControl);

        $input = new InterviewAssignInput();
        $input->applicationId = 5;
        $input->interviewerId = 10;
        $input->interviewSchemaId = 1;

        $processor->process($input, new Post());
    }
}

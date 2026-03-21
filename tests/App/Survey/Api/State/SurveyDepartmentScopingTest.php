<?php

namespace App\Tests\App\Survey\Api\State;

use ApiPlatform\Metadata\Put;
use App\Identity\Infrastructure\AccessControlService;
use App\Identity\Infrastructure\Entity\User;
use App\Organization\Infrastructure\Entity\Department;
use App\Shared\Repository\SemesterRepository;
use App\Survey\Api\Resource\AdminSurveyWriteResource;
use App\Survey\Api\State\AdminSurveyEditProcessor;
use App\Survey\Infrastructure\Entity\Survey;
use App\Survey\Infrastructure\Repository\SurveyRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class SurveyDepartmentScopingTest extends TestCase
{
    public function testEditDeniedWhenUserFromDifferentDepartment(): void
    {
        $this->expectException(AccessDeniedHttpException::class);

        $deptA = $this->createMock(Department::class);
        $deptA->method('getId')->willReturn(1);

        $deptB = $this->createMock(Department::class);
        $deptB->method('getId')->willReturn(2);

        $user = $this->createMock(User::class);
        $user->method('getDepartment')->willReturn($deptA);

        $survey = $this->createMock(Survey::class);
        $survey->method('getDepartment')->willReturn($deptB);

        $surveyRepo = $this->createMock(SurveyRepository::class);
        $surveyRepo->method('find')->willReturn($survey);

        $em = $this->createMock(EntityManagerInterface::class);
        $semesterRepo = $this->createMock(SemesterRepository::class);

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($user);

        $accessControl = $this->createMock(AccessControlService::class);
        $accessControl->expects($this->once())
            ->method('assertDepartmentAccess')
            ->with($deptB, $user)
            ->willThrowException(new AccessDeniedHttpException('You do not have access to this department.'));

        $processor = new AdminSurveyEditProcessor($em, $surveyRepo, $semesterRepo, $security, $accessControl);

        $data = new AdminSurveyWriteResource();
        $data->name = 'Updated Survey';

        $processor->process($data, new Put(), ['id' => 1]);
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\App\Admission\Api\State;

use ApiPlatform\Metadata\Get;
use App\Admission\Api\Resource\AdminApplicationListResource;
use App\Admission\Api\State\AdminApplicationListProvider;
use App\Admission\Domain\Rules\ApplicationStatusRule;
use App\Admission\Domain\ValueObjects\ApplicationStatus;
use App\Admission\Infrastructure\Entity\AdmissionPeriod;
use App\Admission\Infrastructure\Repository\AdmissionPeriodRepository;
use App\Admission\Infrastructure\Repository\ApplicationRepository;
use App\Identity\Infrastructure\AccessControlService;
use App\Identity\Infrastructure\Entity\User;
use App\Interview\Infrastructure\Entity\Interview;
use App\Organization\Infrastructure\Entity\Department;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Shared\Entity\Semester;
use App\Shared\Repository\SemesterRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class AdminApplicationListProviderTest extends TestCase
{
    private function makeProvider(
        ApplicationRepository $appRepo,
        ApplicationStatusRule $rule,
        ?User $user = null,
        string $status = 'new',
    ): AdminApplicationListProvider {
        $department = $this->createMock(Department::class);
        $semester = $this->createMock(Semester::class);
        $admissionPeriod = $this->createMock(AdmissionPeriod::class);

        if ($user === null) {
            $user = $this->createMock(User::class);
        }
        $user->method('getDepartment')->willReturn($department);

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($user);

        $accessControl = $this->createMock(AccessControlService::class);

        $semesterRepo = $this->createMock(SemesterRepository::class);
        $semesterRepo->method('findOrCreateCurrentSemester')->willReturn($semester);

        $admissionPeriodRepo = $this->createMock(AdmissionPeriodRepository::class);
        $admissionPeriodRepo->method('findOneByDepartmentAndSemester')
            ->willReturn($admissionPeriod);

        $departmentRepo = $this->createMock(DepartmentRepository::class);

        $request = $this->createMock(Request::class);
        $queryBag = new InputBag(['status' => $status]);
        $request->query = $queryBag;

        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->method('getCurrentRequest')->willReturn($request);

        return new AdminApplicationListProvider(
            $accessControl,
            $security,
            $appRepo,
            $admissionPeriodRepo,
            $departmentRepo,
            $semesterRepo,
            $requestStack,
            $rule,
        );
    }

    public function testApplicationItemIncludesApplicationStatusKey(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getFirstName')->willReturn('Ola');
        $user->method('getLastName')->willReturn('Normann');
        $user->method('getEmail')->willReturn('ola@example.com');
        $user->method('isActiveAssistant')->willReturn(false);
        $user->method('getDepartment')->willReturn($this->createMock(Department::class));

        $app = $this->createMock(\App\Admission\Infrastructure\Entity\Application::class);
        $app->method('getId')->willReturn(42);
        $app->method('getUser')->willReturn($user);
        $app->method('getInterview')->willReturn(null);
        $app->method('getPreviousParticipation')->willReturn(false);

        $appRepo = $this->createMock(ApplicationRepository::class);
        $appRepo->method('findNewApplicationsByAdmissionPeriod')->willReturn([$app]);

        $applicationStatus = new ApplicationStatus(ApplicationStatus::APPLICATION_RECEIVED, 'Søknad mottatt', 'Vent');
        $rule = $this->createMock(ApplicationStatusRule::class);
        $rule->method('determine')->willReturn($applicationStatus);

        $provider = $this->makeProvider($appRepo, $rule, $user);
        $result = $provider->provide(new Get(), []);

        $this->assertInstanceOf(AdminApplicationListResource::class, $result);
        $this->assertCount(1, $result->applications);
        $this->assertArrayHasKey('applicationStatus', $result->applications[0]);
        $this->assertSame(ApplicationStatus::APPLICATION_RECEIVED, $result->applications[0]['applicationStatus']);
    }

    public function testApplicationStatusIsAssignedToSchoolWhenUserIsActiveAssistant(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getFirstName')->willReturn('Kari');
        $user->method('getLastName')->willReturn('Hansen');
        $user->method('getEmail')->willReturn('kari@example.com');
        $user->method('isActiveAssistant')->willReturn(true);
        $user->method('getDepartment')->willReturn($this->createMock(Department::class));

        $app = $this->createMock(\App\Admission\Infrastructure\Entity\Application::class);
        $app->method('getId')->willReturn(1);
        $app->method('getUser')->willReturn($user);
        $app->method('getInterview')->willReturn(null);
        $app->method('getPreviousParticipation')->willReturn(false);

        $appRepo = $this->createMock(ApplicationRepository::class);
        $appRepo->method('findNewApplicationsByAdmissionPeriod')->willReturn([$app]);

        // Use real rule to verify the full integration
        $rule = new ApplicationStatusRule();

        $provider = $this->makeProvider($appRepo, $rule, $user);
        $result = $provider->provide(new Get(), []);

        $this->assertSame(ApplicationStatus::ASSIGNED_TO_SCHOOL, $result->applications[0]['applicationStatus']);
    }

    public function testApplicationStatusIsInvitedToInterviewWhenInterviewPending(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getFirstName')->willReturn('Per');
        $user->method('getLastName')->willReturn('Olsen');
        $user->method('getEmail')->willReturn('per@example.com');
        $user->method('isActiveAssistant')->willReturn(false);
        $user->method('getDepartment')->willReturn($this->createMock(Department::class));

        // InterviewStatusType::PENDING = 0
        $interview = $this->createMock(Interview::class);
        $interview->method('getInterviewed')->willReturn(false);
        $interview->method('getInterviewStatus')->willReturn(0); // PENDING
        $interview->method('getInterviewStatusAsString')->willReturn('pending');
        $interview->method('getScheduled')->willReturn(null);
        $interview->method('getRoom')->willReturn(null);
        $interview->method('getInterviewer')->willReturn(null);

        $app = $this->createMock(\App\Admission\Infrastructure\Entity\Application::class);
        $app->method('getId')->willReturn(2);
        $app->method('getUser')->willReturn($user);
        $app->method('getInterview')->willReturn($interview);
        $app->method('getPreviousParticipation')->willReturn(false);

        $appRepo = $this->createMock(ApplicationRepository::class);
        $appRepo->method('findNewApplicationsByAdmissionPeriod')->willReturn([$app]);

        $rule = new ApplicationStatusRule();

        $provider = $this->makeProvider($appRepo, $rule, $user);
        $result = $provider->provide(new Get(), []);

        $this->assertSame(ApplicationStatus::INVITED_TO_INTERVIEW, $result->applications[0]['applicationStatus']);
    }

    public function testApplicationStatusIsApplicationReceivedWhenNoInterview(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getFirstName')->willReturn('Lise');
        $user->method('getLastName')->willReturn('Berg');
        $user->method('getEmail')->willReturn('lise@example.com');
        $user->method('isActiveAssistant')->willReturn(false);
        $user->method('getDepartment')->willReturn($this->createMock(Department::class));

        $app = $this->createMock(\App\Admission\Infrastructure\Entity\Application::class);
        $app->method('getId')->willReturn(3);
        $app->method('getUser')->willReturn($user);
        $app->method('getInterview')->willReturn(null);
        $app->method('getPreviousParticipation')->willReturn(false);

        $appRepo = $this->createMock(ApplicationRepository::class);
        $appRepo->method('findNewApplicationsByAdmissionPeriod')->willReturn([$app]);

        $rule = new ApplicationStatusRule();

        $provider = $this->makeProvider($appRepo, $rule, $user);
        $result = $provider->provide(new Get(), []);

        $this->assertSame(ApplicationStatus::APPLICATION_RECEIVED, $result->applications[0]['applicationStatus']);
    }
}

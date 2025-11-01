<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\Application;
use AppBundle\Entity\Department;
use AppBundle\Entity\FieldOfStudy;
use AppBundle\Entity\Interview;
use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Role\Roles;
use AppBundle\Service\ApplicationAdmission;
use AppBundle\Service\Contract\LoginManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

class ApplicationAdmissionTest extends TestCase
{
    /**
     * @var ApplicationAdmission
     */
    private $service;

    /**
     * @var EntityManagerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $em;

    protected function setUp()
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $twig = $this->createMock(Environment::class);
        $loginManager = $this->createMock(LoginManagerInterface::class);

        $this->service = new ApplicationAdmission($this->em, $twig, $loginManager);
    }

    public function testUserHasAlreadyAppliedInAdmissionPeriodReturnsTrue()
    {
        $user = new User();
        $user->setEmail('test@example.com');

        $admissionPeriod = new AdmissionPeriod();

        $repo = $this->createMock(EntityRepository::class);
        $repo->expects($this->once())
            ->method('findByEmailInAdmissionPeriod')
            ->with('test@example.com', $admissionPeriod)
            ->willReturn([new Application()]);

        $this->em->expects($this->once())
            ->method('getRepository')
            ->with(Application::class)
            ->willReturn($repo);

        $result = $this->service->userHasAlreadyAppliedInAdmissionPeriod($user, $admissionPeriod);

        $this->assertTrue($result);
    }

    public function testUserHasAlreadyAppliedInAdmissionPeriodReturnsFalse()
    {
        $user = new User();
        $user->setEmail('test@example.com');

        $admissionPeriod = new AdmissionPeriod();

        $repo = $this->createMock(EntityRepository::class);
        $repo->expects($this->once())
            ->method('findByEmailInAdmissionPeriod')
            ->with('test@example.com', $admissionPeriod)
            ->willReturn([]);

        $this->em->expects($this->once())
            ->method('getRepository')
            ->with(Application::class)
            ->willReturn($repo);

        $result = $this->service->userHasAlreadyAppliedInAdmissionPeriod($user, $admissionPeriod);

        $this->assertFalse($result);
    }

    public function testCreateApplicationForExistingAssistant()
    {
        $user = new User();
        $department = new Department();
        $user->setDepartment($department);

        $admissionPeriod = new AdmissionPeriod();
        $lastInterview = new Interview();

        $admissionRepo = $this->createMock(EntityRepository::class);
        $admissionRepo->expects($this->once())
            ->method('findOneWithActiveAdmissionByDepartment')
            ->with($department)
            ->willReturn($admissionPeriod);

        $applicationRepo = $this->createMock(EntityRepository::class);
        $applicationRepo->expects($this->once())
            ->method('findByUserInAdmissionPeriod')
            ->with($user, $admissionPeriod)
            ->willReturn(null);

        $interviewRepo = $this->createMock(EntityRepository::class);
        $interviewRepo->expects($this->once())
            ->method('findLatestInterviewByUser')
            ->with($user)
            ->willReturn($lastInterview);

        $this->em->expects($this->exactly(3))
            ->method('getRepository')
            ->willReturnCallback(function ($class) use ($admissionRepo, $applicationRepo, $interviewRepo) {
                if ($class === AdmissionPeriod::class) {
                    return $admissionRepo;
                }
                if ($class === Application::class) {
                    return $applicationRepo;
                }
                if ($class === Interview::class) {
                    return $interviewRepo;
                }
                return null;
            });

        $result = $this->service->createApplicationForExistingAssistant($user);

        $this->assertInstanceOf(Application::class, $result);
        $this->assertSame($user, $result->getUser());
        $this->assertSame($admissionPeriod, $result->getAdmissionPeriod());
        $this->assertTrue($result->getPreviousParticipation());
        $this->assertSame($lastInterview, $result->getInterview());
    }

    public function testSetCorrectUser()
    {
        $existingUser = new User();
        $existingUser->setEmail('test@example.com');

        $newUser = new User();
        $newUser->setEmail('test@example.com');

        $application = new Application();
        $application->setUser($newUser);

        $role = new Role();
        $role->setRole(Roles::ASSISTANT);

        $userRepo = $this->createMock(EntityRepository::class);
        $userRepo->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => 'test@example.com'])
            ->willReturn($existingUser);

        $roleRepo = $this->createMock(EntityRepository::class);
        $roleRepo->expects($this->once())
            ->method('findByRoleName')
            ->with(Roles::ASSISTANT)
            ->willReturn($role);

        $this->em->expects($this->exactly(2))
            ->method('getRepository')
            ->willReturnCallback(function ($class) use ($userRepo, $roleRepo) {
                if ($class === User::class) {
                    return $userRepo;
                }
                if ($class === Role::class) {
                    return $roleRepo;
                }
                return null;
            });

        $this->service->setCorrectUser($application);

        $this->assertSame($existingUser, $application->getUser());
    }
}


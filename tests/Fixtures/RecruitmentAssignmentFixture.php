<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use App\Admission\Infrastructure\Entity\AdmissionPeriod;
use App\Admission\Infrastructure\Entity\Application;
use App\Identity\Infrastructure\Entity\Role;
use App\Identity\Infrastructure\Entity\User;
use App\Interview\Infrastructure\Entity\InterviewSchema;
use App\Organization\Infrastructure\Entity\Department;
use App\Organization\Infrastructure\Entity\FieldOfStudy;
use App\Shared\Entity\Semester;
use App\Shared\SemesterUtil;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RecruitmentAssignmentFixture extends AbstractFixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['recruitment-assignment'];
    }

    public function load(ObjectManager $manager): void
    {
        $now = new \DateTime('now');

        $teamLeaderRole = new Role('ROLE_TEAM_LEADER');
        $teamLeaderRole->setName('E2E team leader');
        $manager->persist($teamLeaderRole);

        $userRole = new Role('ROLE_USER');
        $userRole->setName('E2E user');
        $manager->persist($userRole);

        $department = new Department();
        $department->setName('E2E recruitment department');
        $department->setShortName('E2E');
        $department->setEmail('recruitment-0028@example.invalid');
        $department->setCity('E2E City');
        $department->setAddress('E2E only');
        $manager->persist($department);

        $fieldOfStudy = new FieldOfStudy();
        $fieldOfStudy->setName('E2E computer science');
        $fieldOfStudy->setShortName('E2E-CS');
        $fieldOfStudy->setDepartment($department);
        $department->addFieldOfStudy($fieldOfStudy);
        $manager->persist($fieldOfStudy);

        $semester = SemesterUtil::timeToSemester($now);
        $manager->persist($semester);

        $admissionPeriod = new AdmissionPeriod();
        $admissionPeriod->setDepartment($department);
        $admissionPeriod->setSemester($semester);
        $admissionPeriod->setStartDate((clone $now)->modify('-1 day'));
        $admissionPeriod->setEndDate((clone $now)->modify('+1 day'));
        $department->addAdmissionPeriod($admissionPeriod);
        $manager->persist($admissionPeriod);

        $leader = $this->createUser(
            'recruitment-leader-0028',
            'recruitment-leader-0028@example.invalid',
            'Teamleder',
            '0028',
            $teamLeaderRole,
            $fieldOfStudy,
            'recruitment-e2e-0028',
        );
        $manager->persist($leader);

        $interviewer = $this->createUser(
            'recruitment-interviewer-0028',
            'recruitment-interviewer-0028@example.invalid',
            'Intervjuer',
            '0028',
            $teamLeaderRole,
            $fieldOfStudy,
            'recruitment-e2e-0028',
        );
        $manager->persist($interviewer);

        $applicant = $this->createUser(
            'recruitment-applicant-0028',
            'recruitment-applicant-0028@example.invalid',
            'Søker',
            '0028',
            $userRole,
            $fieldOfStudy,
            'recruitment-e2e-0028',
        );
        $manager->persist($applicant);

        $schema = new InterviewSchema();
        $schema->setName('Førstegangsintervju 0028');
        $manager->persist($schema);

        $application = new Application();
        $application->setUser($applicant);
        $application->setAdmissionPeriod($admissionPeriod);
        $application->setYearOfStudy('1');
        $application->setMonday(true);
        $application->setTuesday(true);
        $application->setWednesday(true);
        $application->setThursday(true);
        $application->setFriday(true);
        $application->setPreviousParticipation(false);
        $application->setLanguage('Norsk');
        $application->setDoublePosition(false);
        $application->setTeamInterest(false);
        $application->setHeardAboutFrom(['E2E']);
        $application->setCreated(clone $now);
        $application->setLastEdited(clone $now);
        $manager->persist($application);

        $manager->flush();
    }

    private function createUser(
        string $username,
        string $email,
        string $firstName,
        string $lastName,
        Role $role,
        FieldOfStudy $fieldOfStudy,
        string $password,
    ): User {
        $user = new User();
        $user->setActive(true);
        $user->setEmail($email);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setGender(false);
        $user->setPhone('00000000');
        $user->setUserName($username);
        $user->setPassword($password);
        $user->setFieldOfStudy($fieldOfStudy);
        $user->setPicturePath('images/defaultProfile.png');
        $user->addRole($role);

        return $user;
    }
}

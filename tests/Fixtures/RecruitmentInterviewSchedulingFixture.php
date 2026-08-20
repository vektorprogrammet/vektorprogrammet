<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use App\Admission\Infrastructure\Entity\AdmissionPeriod;
use App\Admission\Infrastructure\Entity\Application;
use App\Identity\Infrastructure\Entity\Role;
use App\Identity\Infrastructure\Entity\User;
use App\Interview\Infrastructure\Entity\Interview;
use App\Interview\Infrastructure\Entity\InterviewSchema;
use App\Organization\Infrastructure\Entity\Department;
use App\Organization\Infrastructure\Entity\FieldOfStudy;
use App\Shared\SemesterUtil;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RecruitmentInterviewSchedulingFixture extends AbstractFixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['recruitment-interview-scheduling'];
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
        $department->setName('E2E scheduling department');
        $department->setShortName('E2E29');
        $department->setEmail('recruitment-0029@example.invalid');
        $department->setCity('E2E City');
        $department->setAddress('E2E only');
        $manager->persist($department);

        $fieldOfStudy = new FieldOfStudy();
        $fieldOfStudy->setName('E2E scheduling studies');
        $fieldOfStudy->setShortName('E2E29-CS');
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
            'recruitment-leader-0029',
            'recruitment-leader-0029@example.invalid',
            'Teamleder',
            '0029',
            $teamLeaderRole,
            $fieldOfStudy,
            'recruitment-e2e-0029',
        );
        $manager->persist($leader);

        $interviewer = $this->createUser(
            'recruitment-interviewer-0029',
            'recruitment-interviewer-0029@example.invalid',
            'Intervjuer',
            '0029',
            $teamLeaderRole,
            $fieldOfStudy,
            'recruitment-e2e-0029',
        );
        $manager->persist($interviewer);

        $applicant = $this->createUser(
            'recruitment-applicant-0029',
            'recruitment-applicant-0029@example.invalid',
            'Søker',
            '0029',
            $userRole,
            $fieldOfStudy,
            'recruitment-e2e-0029',
        );
        $manager->persist($applicant);

        $schema = new InterviewSchema();
        $schema->setName('Førstegangsintervju 0029');
        $manager->persist($schema);

        $interview = new Interview();
        $interview->setUser($applicant);
        $interview->setInterviewer($interviewer);
        $interview->setInterviewSchema($schema);
        $interview->setResponseCode('recruitment-response-0029');

        $application = new Application();
        $application->setUser($applicant);
        $application->setAdmissionPeriod($admissionPeriod);
        $application->setInterview($interview);
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

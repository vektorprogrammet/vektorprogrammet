<?php

declare(strict_types=1);

namespace App\Support\DataFixtures\ORM;

use App\Admission\Infrastructure\Entity\AdmissionPeriod;
use App\Admission\Infrastructure\Entity\Application;
use App\Identity\Infrastructure\Entity\Role;
use App\Identity\Infrastructure\Entity\User;
use App\Interview\Domain\ValueObjects\InterviewStatusType;
use App\Interview\Infrastructure\Entity\Interview;
use App\Interview\Infrastructure\Entity\InterviewSchema;
use App\Organization\Infrastructure\Entity\Department;
use App\Organization\Infrastructure\Entity\FieldOfStudy;
use App\Organization\Infrastructure\Entity\Position;
use App\Organization\Infrastructure\Entity\Team;
use App\Organization\Infrastructure\Entity\TeamMembership;
use App\Shared\Entity\Semester;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class InterviewRecruiterFixture extends AbstractFixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['background-operations'];
    }

    public function load(ObjectManager $manager): void
    {
        $now = new \DateTime('now', new \DateTimeZone('Europe/Oslo'));
        $semester = $this->getCurrentSemester($manager, $now);

        $teamLeaderRole = $this->getOrCreateRole($manager, 'ROLE_TEAM_LEADER', 'Background team leader');
        $teamMemberRole = $this->getOrCreateRole($manager, 'ROLE_TEAM_MEMBER', 'Background team member');
        $userRole = $this->getOrCreateRole($manager, 'ROLE_USER', 'Background user');

        $department = new Department();
        $department->setName('Background recruiter department');
        $department->setShortName('BG-REC');
        $department->setEmail('background-recruiter@example.invalid');
        $department->setCity('Background Recruiter City');
        $department->setAddress('Disposable background fixture');
        $department->setActive(true);
        $manager->persist($department);

        $fieldOfStudy = new FieldOfStudy();
        $fieldOfStudy->setName('Background recruiter studies');
        $fieldOfStudy->setShortName('BG-REC-STUDY');
        $fieldOfStudy->setDepartment($department);
        $department->addFieldOfStudy($fieldOfStudy);
        $manager->persist($fieldOfStudy);

        $admissionPeriod = new AdmissionPeriod();
        $admissionPeriod->setDepartment($department);
        $admissionPeriod->setSemester($semester);
        $admissionPeriod->setStartDate((clone $now)->modify('-2 days'));
        $admissionPeriod->setEndDate((clone $now)->modify('+30 days'));
        $department->addAdmissionPeriod($admissionPeriod);
        $manager->persist($admissionPeriod);

        $team = new Team();
        $team->setName('Background recruiter team');
        $team->setEmail('background-recruiter-team@example.invalid');
        $team->setDepartment($department);
        $team->setDescription('Disposable team for recruiter and automation evidence.');
        $team->setShortDescription('Background recruiter team');
        $team->setAcceptApplication(true);
        $team->setDeadline((clone $now)->modify('+30 days'));
        $team->setActive(true);
        $department->addTeam($team);
        $manager->persist($team);

        $leader = $this->createUser(
            'background-recruiter-leader-0032',
            'background-recruiter-leader-0032@example.invalid',
            'Recruiter',
            'Leader 0032',
            $teamLeaderRole,
            $fieldOfStudy,
            'background-recruiter-password-0032',
        );
        $manager->persist($leader);

        $interviewer = $this->createUser(
            'background-recruiter-interviewer-0032',
            'background-recruiter-interviewer-0032@example.invalid',
            'Recruiter',
            'Interviewer 0032',
            $teamMemberRole,
            $fieldOfStudy,
            'background-recruiter-password-0032',
        );
        $manager->persist($interviewer);

        $applicant = $this->createUser(
            'background-recruiter-applicant-0032',
            'background-recruiter-applicant-0032@example.invalid',
            'Applicant',
            'Assignment 0032',
            $userRole,
            $fieldOfStudy,
            'background-recruiter-password-0032',
        );
        $manager->persist($applicant);

        $deliveryApplicant = $this->createUser(
            'background-delivery-applicant-0032',
            'background-delivery-applicant-0032@example.invalid',
            'Applicant',
            'Reminder 0032',
            $userRole,
            $fieldOfStudy,
            'background-delivery-password-0032',
        );
        $manager->persist($deliveryApplicant);

        $this->addMembership($manager, $leader, $team, $semester, true, 'Background leader');
        $this->addMembership($manager, $interviewer, $team, $semester, false, 'Background interviewer');

        $schema = new InterviewSchema();
        $schema->setName('Background recruiter schema 0032');
        $manager->persist($schema);

        $assignment = new Application();
        $this->configureApplication($assignment, $applicant, $admissionPeriod, $now);
        $manager->persist($assignment);

        $deliveryInterview = new Interview();
        $deliveryInterview->setUser($deliveryApplicant);
        $deliveryInterview->setInterviewer($interviewer);
        $deliveryInterview->setInterviewSchema($schema);
        $deliveryInterview->setScheduled((clone $now)->modify('+2 days'));
        $deliveryInterview->setRoom('Background room 0032');
        $deliveryInterview->setCampus('Background campus');
        $deliveryInterview->setMapLink('https://maps.example.invalid/background-0032');
        $deliveryInterview->setResponseCode('background-delivery-response-0032');
        $deliveryInterview->setInterviewStatus(InterviewStatusType::PENDING);
        $lastScheduleChanged = new \ReflectionProperty(Interview::class, 'lastScheduleChanged');
        $lastScheduleChanged->setAccessible(true);
        $lastScheduleChanged->setValue($deliveryInterview, (clone $now)->modify('-2 days'));

        $deliveryApplication = new Application();
        $this->configureApplication($deliveryApplication, $deliveryApplicant, $admissionPeriod, $now);
        $deliveryApplication->setInterview($deliveryInterview);
        $manager->persist($deliveryApplication);

        $manager->flush();
    }

    private function getCurrentSemester(ObjectManager $manager, \DateTime $now): Semester
    {
        $semester = $manager->getRepository(Semester::class)->findOneBy([
            'year' => $now->format('Y'),
            'semesterTime' => $now->format('m') <= 7 ? 'Vår' : 'Høst',
        ]);
        if ($semester instanceof Semester) {
            return $semester;
        }

        $semester = new Semester();
        $semester->setYear($now->format('Y'));
        $semester->setSemesterTime($now->format('m') <= 7 ? 'Vår' : 'Høst');
        $manager->persist($semester);

        return $semester;
    }

    private function getOrCreateRole(ObjectManager $manager, string $roleName, string $label): Role
    {
        $existing = $manager->getRepository(Role::class)->findOneBy(['role' => $roleName]);
        if ($existing instanceof Role) {
            return $existing;
        }

        $role = new Role($roleName);
        $role->setName($label);
        $manager->persist($role);

        return $role;
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
        $user->setPhone('90000032');
        $user->setUserName($username);
        $user->setPassword($password);
        $user->setFieldOfStudy($fieldOfStudy);
        $user->setPicturePath('images/defaultProfile.png');
        $user->addRole($role);

        return $user;
    }

    private function addMembership(
        ObjectManager $manager,
        User $user,
        Team $team,
        Semester $semester,
        bool $isTeamLeader,
        string $positionName,
    ): void {
        $position = new Position();
        $position->setName($positionName);
        $manager->persist($position);

        $membership = new TeamMembership();
        $membership->setUser($user);
        $membership->setTeam($team);
        $membership->setPosition($position);
        $membership->setStartSemester($semester);
        $membership->setEndSemester(null);
        $membership->setIsTeamLeader($isTeamLeader);
        $membership->setIsSuspended(false);
        $manager->persist($membership);
    }

    private function configureApplication(
        Application $application,
        User $user,
        AdmissionPeriod $admissionPeriod,
        \DateTime $now,
    ): void {
        $application->setUser($user);
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
        $application->setHeardAboutFrom(['background-fixture']);
        $application->setCreated(clone $now);
        $application->setLastEdited(clone $now);
    }
}

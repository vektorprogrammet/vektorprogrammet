<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use App\Admission\Infrastructure\Entity\AdmissionPeriod;
use App\Admission\Infrastructure\Entity\Application;
use App\Identity\Infrastructure\Entity\Role;
use App\Identity\Infrastructure\Entity\User;
use App\Interview\Domain\ValueObjects\InterviewStatusType;
use App\Interview\Infrastructure\Entity\Interview;
use App\Interview\Infrastructure\Entity\InterviewSchema;
use App\Organization\Infrastructure\Entity\Department;
use App\Organization\Infrastructure\Entity\FieldOfStudy;
use App\Shared\SemesterUtil;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class RecruitmentInterviewInvitationResponseFixture extends AbstractFixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['recruitment-interview-invitation-response'];
    }

    public function load(ObjectManager $manager): void
    {
        $now = new \DateTime('now', new \DateTimeZone('Europe/Oslo'));
        $scheduledAt = (clone $now)->modify('+7 days')->setTime(15, 0);

        $teamLeaderRole = new Role('ROLE_TEAM_LEADER');
        $teamLeaderRole->setName('E2E response team leader');
        $manager->persist($teamLeaderRole);
        $interviewerRole = new Role('ROLE_TEAM_MEMBER');
        $interviewerRole->setName('E2E response interviewer');
        $manager->persist($interviewerRole);

        $userRole = new Role('ROLE_USER');
        $userRole->setName('E2E response applicant');
        $manager->persist($userRole);

        $department = new Department();
        $department->setName('E2E response department');
        $department->setShortName('E2E31');
        $department->setEmail('recruitment-response-0031@example.invalid');
        $department->setCity('E2E City');
        $department->setAddress('E2E only');
        $manager->persist($department);

        $fieldOfStudy = new FieldOfStudy();
        $fieldOfStudy->setName('E2E response studies');
        $fieldOfStudy->setShortName('E2E31-CS');
        $fieldOfStudy->setDepartment($department);
        $department->addFieldOfStudy($fieldOfStudy);
        $manager->persist($fieldOfStudy);

        $semester = SemesterUtil::timeToSemester($now);
        $manager->persist($semester);

        $admissionPeriod = new AdmissionPeriod();
        $admissionPeriod->setDepartment($department);
        $admissionPeriod->setSemester($semester);
        $admissionPeriod->setStartDate((clone $scheduledAt)->modify('-30 days'));
        $admissionPeriod->setEndDate((clone $scheduledAt)->modify('+30 days'));
        $department->addAdmissionPeriod($admissionPeriod);
        $manager->persist($admissionPeriod);

        $leader = $this->createUser(
            'recruitment-response-leader-0031',
            'recruitment-response-leader-0031@example.invalid',
            'Teamleder',
            '0031',
            $teamLeaderRole,
            $fieldOfStudy,
            'recruitment-response-e2e-0031',
        );
        $manager->persist($leader);

        $interviewer = $this->createUser(
            'recruitment-response-interviewer-0031',
            'recruitment-response-interviewer-0031@example.invalid',
            'Intervjuer',
            '0031',
            $interviewerRole,
            $fieldOfStudy,
            'recruitment-response-e2e-0031',
        );
        $manager->persist($interviewer);

        $schema = new InterviewSchema();
        $schema->setName('Førstegangsintervju 0031');
        $manager->persist($schema);

        $this->createScheduledInterview(
            $manager,
            $this->createApplicant($manager, $userRole, $fieldOfStudy, 'confirm'),
            $interviewer,
            $schema,
            $admissionPeriod,
            $scheduledAt,
            'recruitment_response_0031_confirm',
            'Rom 31 confirm',
        );
        $this->createScheduledInterview(
            $manager,
            $this->createApplicant($manager, $userRole, $fieldOfStudy, 'reject'),
            $interviewer,
            $schema,
            $admissionPeriod,
            $scheduledAt,
            'recruitment_response_0031_reject',
            'Rom 31 reject',
        );
        $this->createScheduledInterview(
            $manager,
            $this->createApplicant($manager, $userRole, $fieldOfStudy, 'new-time'),
            $interviewer,
            $schema,
            $admissionPeriod,
            $scheduledAt,
            'recruitment_response_0031_new_time',
            'Rom 31 new time',
        );

        $manager->flush();
    }

    private function createApplicant(
        ObjectManager $manager,
        Role $role,
        FieldOfStudy $fieldOfStudy,
        string $variant,
    ): User {
        $applicant = $this->createUser(
            "recruitment-response-applicant-0031-$variant",
            "recruitment-response-applicant-0031-$variant@example.invalid",
            'Søker',
            ucfirst($variant).' 0031',
            $role,
            $fieldOfStudy,
            'recruitment-response-e2e-0031',
        );
        $manager->persist($applicant);

        return $applicant;
    }

    private function createScheduledInterview(
        ObjectManager $manager,
        User $applicant,
        User $interviewer,
        InterviewSchema $schema,
        AdmissionPeriod $admissionPeriod,
        \DateTime $scheduledAt,
        string $responseCode,
        string $room,
    ): void {
        $interview = new Interview();
        $interview->setUser($applicant);
        $interview->setInterviewer($interviewer);
        $interview->setInterviewSchema($schema);
        $interview->setScheduled(clone $scheduledAt);
        $interview->setRoom($room);
        $interview->setCampus('Gløshaugen');
        $interview->setMapLink('https://maps.example.invalid/interview-0031');
        $interview->setResponseCode($responseCode);
        $interview->setInterviewStatus(InterviewStatusType::PENDING);

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
        $application->setCreated(clone $scheduledAt);
        $application->setLastEdited(clone $scheduledAt);
        $manager->persist($application);
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

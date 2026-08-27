<?php

declare(strict_types=1);

namespace App\Support\DataFixtures\ORM;

use App\Admission\Infrastructure\Entity\AdmissionPeriod;
use App\Admission\Infrastructure\Entity\AdmissionSubscriber;
use App\Admission\Infrastructure\Entity\Application;
use App\Admission\Infrastructure\Entity\InfoMeeting;
use App\Identity\Infrastructure\Entity\Role;
use App\Identity\Infrastructure\Entity\User;
use App\Interview\Domain\ValueObjects\InterviewStatusType;
use App\Interview\Infrastructure\Entity\Interview;
use App\Interview\Infrastructure\Entity\InterviewSchema;
use App\Organization\Infrastructure\Entity\Department;
use App\Organization\Infrastructure\Entity\FieldOfStudy;
use App\Shared\Entity\Semester;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Clock\Clock;

final class BackgroundDeliveryFixture extends AbstractFixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['background-operations'];
    }

    public function load(ObjectManager $manager): void
    {
        $now = \DateTime::createFromInterface(
            Clock::get()->withTimeZone(new \DateTimeZone('Europe/Oslo'))->now(),
        );
        $semester = $this->getCurrentSemester($manager, $now);
        $teamMemberRole = $this->getOrCreateRole($manager, 'ROLE_TEAM_MEMBER', 'Background team member');
        $userRole = $this->getOrCreateRole($manager, 'ROLE_USER', 'Background user');

        $department = new Department();
        $department->setName('Background delivery department');
        $department->setShortName('BG-DEL');
        $department->setEmail('background-delivery@example.invalid');
        $department->setCity('Background Delivery City');
        $department->setAddress('Disposable delivery fixture');
        $department->setActive(true);
        $manager->persist($department);

        $fieldOfStudy = new FieldOfStudy();
        $fieldOfStudy->setName('Background delivery studies');
        $fieldOfStudy->setShortName('BG-DEL-STUDY');
        $fieldOfStudy->setDepartment($department);
        $department->addFieldOfStudy($fieldOfStudy);
        $manager->persist($fieldOfStudy);

        $admissionPeriod = new AdmissionPeriod();
        $admissionPeriod->setDepartment($department);
        $admissionPeriod->setSemester($semester);
        $admissionPeriod->setStartDate((clone $now)->modify('-2 days'));
        $admissionPeriod->setEndDate((clone $now)->modify('+30 days'));
        $department->addAdmissionPeriod($admissionPeriod);
        $infoMeeting = new InfoMeeting();
        $infoMeeting->setDate((clone $now)->modify('+2 hours'));
        $infoMeeting->setShowOnPage(true);
        $infoMeeting->setRoom('Background delivery info room');
        $infoMeeting->setDescription('Disposable info meeting for delivery evidence.');
        $infoMeeting->setLink('https://example.invalid/background-delivery-info');
        $admissionPeriod->setInfoMeeting($infoMeeting);
        $manager->persist($admissionPeriod);

        $subscriber = new AdmissionSubscriber();
        $subscriber->setDepartment($department);
        $subscriber->setEmail('background-delivery-subscriber-0032@example.invalid');
        $subscriber->setTimestamp(clone $now);
        $subscriber->setUnsubscribeCode('background-delivery-unsubscribe-0032');
        $subscriber->setInfoMeeting(true);
        $subscriber->setFromApplication(false);
        $manager->persist($subscriber);

        $interviewer = $this->createUser(
            'background-delivery-interviewer-0032',
            'background-delivery-interviewer-0032@example.invalid',
            'Delivery',
            'Interviewer 0032',
            $teamMemberRole,
            $fieldOfStudy,
            'background-delivery-password-0032',
        );
        $manager->persist($interviewer);

        $applicant = $this->createUser(
            'background-delivery-reminder-0032',
            'background-delivery-reminder-0032@example.invalid',
            'Delivery',
            'Reminder 0032',
            $userRole,
            $fieldOfStudy,
            'background-delivery-password-0032',
        );
        $manager->persist($applicant);

        $schema = new InterviewSchema();
        $schema->setName('Background delivery schema 0032');
        $manager->persist($schema);

        $interview = new Interview();
        $interview->setUser($applicant);
        $interview->setInterviewer($interviewer);
        $interview->setInterviewSchema($schema);
        $interview->setScheduled((clone $now)->modify('+2 days'));
        $interview->setRoom('Background delivery room');
        $interview->setCampus('Background delivery campus');
        $interview->setMapLink('https://maps.example.invalid/background-delivery-0032');
        $interview->setResponseCode('background_delivery_reminder_response_0032');
        $interview->setInterviewStatus(InterviewStatusType::PENDING);
        $lastScheduleChanged = new \ReflectionProperty(Interview::class, 'lastScheduleChanged');
        $lastScheduleChanged->setAccessible(true);
        $lastScheduleChanged->setValue($interview, (clone $now)->modify('-2 days'));

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
        $application->setHeardAboutFrom(['background-fixture']);
        $application->setCreated(clone $now);
        $application->setLastEdited(clone $now);
        $manager->persist($application);

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
        $user->setPhone('90000034');
        $user->setUserName($username);
        $user->setPassword($password);
        $user->setFieldOfStudy($fieldOfStudy);
        $user->setPicturePath('images/defaultProfile.png');
        $user->addRole($role);

        return $user;
    }
}

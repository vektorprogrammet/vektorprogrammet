<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use App\Identity\Infrastructure\Entity\Role;
use App\Identity\Infrastructure\Entity\User;
use App\Operations\Infrastructure\Entity\Receipt;
use App\Admission\Infrastructure\Entity\AdmissionPeriod;
use App\Organization\Infrastructure\Entity\Department;
use App\Organization\Infrastructure\Entity\FieldOfStudy;
use App\Scheduling\Infrastructure\Entity\School;
use App\Scheduling\Infrastructure\Entity\SchoolCapacity;
use App\Shared\Entity\Semester;
use App\Shared\SemesterUtil;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class OrgOperationsJourneyFixture extends AbstractFixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['org-operations-journeys'];
    }

    public function load(ObjectManager $manager): void
    {
        $adminRole = new Role('ROLE_ADMIN');
        $adminRole->setName('Org operations administrator');
        $manager->persist($adminRole);

        $teamLeaderRole = new Role('ROLE_TEAM_LEADER');
        $teamLeaderRole->setName('Org operations team leader');
        $manager->persist($teamLeaderRole);

        $teamMemberRole = new Role('ROLE_TEAM_MEMBER');
        $teamMemberRole->setName('Org operations team member');
        $manager->persist($teamMemberRole);

        $userRole = new Role('ROLE_USER');
        $userRole->setName('Org operations user');
        $manager->persist($userRole);

        $department = new Department();
        $department->setName('Org operations department');
        $department->setShortName('OPS32');
        $department->setEmail('org-operations-department-0032@example.invalid');
        $department->setCity('OrgOps City 0032');
        $department->setAddress('Org operations fixture');
        $department->setLatitude('63.4305');
        $department->setLongitude('10.3951');
        $manager->persist($department);

        $fieldOfStudy = new FieldOfStudy();
        $fieldOfStudy->setName('Org operations studies');
        $fieldOfStudy->setShortName('OPS32-STUDY');
        $fieldOfStudy->setDepartment($department);
        $department->addFieldOfStudy($fieldOfStudy);
        $manager->persist($fieldOfStudy);

        $currentSemester = SemesterUtil::timeToSemester(new \DateTime('now'));
        $manager->persist($currentSemester);
        $admissionPeriod = new AdmissionPeriod();
        $admissionPeriod->setDepartment($department);
        $admissionPeriod->setSemester($currentSemester);
        $admissionPeriod->setStartDate(new \DateTime('yesterday'));
        $admissionPeriod->setEndDate(new \DateTime('+14 days'));
        $department->addAdmissionPeriod($admissionPeriod);
        $manager->persist($admissionPeriod);


        $school = new School();
        $school->setName('Org operations scheduling school');
        $school->setContactPerson('Scheduling contact 0032');
        $school->setEmail('org-operations-school-0032@example.invalid');
        $school->setPhone('00000032');
        $school->setInternational(false);
        $school->setActive(true);
        $school->addDepartment($department);
        $department->addSchool($school);
        $manager->persist($school);

        $schoolCapacity = new SchoolCapacity();
        $schoolCapacity->setSchool($school);
        $schoolCapacity->setSemester($currentSemester);
        $schoolCapacity->setDepartment($department);
        $schoolCapacity->setMonday(2);
        $schoolCapacity->setTuesday(2);
        $schoolCapacity->setWednesday(2);
        $schoolCapacity->setThursday(2);
        $schoolCapacity->setFriday(2);
        $manager->persist($schoolCapacity);

        $admin = $this->createUser(
            'org-ops-admin-0032',
            'org-ops-admin-0032@example.invalid',
            'Org',
            'Admin',
            $adminRole,
            $fieldOfStudy,
            'org-operations-password-0032',
        );
        $manager->persist($admin);

        $teamLeader = $this->createUser(
            'org-ops-leader-0032',
            'org-ops-leader-0032@example.invalid',
            'Org',
            'Leader',
            $teamLeaderRole,
            $fieldOfStudy,
            'org-operations-password-0032',
        );
        $manager->persist($teamLeader);

        $teamMember = $this->createUser(
            'org-ops-member-0032',
            'org-ops-member-0032@example.invalid',
            'Org',
            'Member',
            $teamMemberRole,
            $fieldOfStudy,
            'org-operations-password-0032',
        );
        $manager->persist($teamMember);

        $user = $this->createUser(
            'org-ops-user-0032',
            'org-ops-user-0032@example.invalid',
            'Org',
            'User',
            $userRole,
            $fieldOfStudy,
            'org-operations-password-0032',
        );
        $manager->persist($user);

        $receipt = new Receipt();
        $receipt->setUser($user);
        $receipt->setSubmitDate(new \DateTime('2026-01-15 12:00:00'));
        $receipt->setReceiptDate(new \DateTime('2026-01-14 12:00:00'));
        $receipt->setDescription('Org operations receipt 0032');
        $receipt->setSum(42.32);
        $manager->persist($receipt);

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

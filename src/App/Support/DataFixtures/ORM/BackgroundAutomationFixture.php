<?php

declare(strict_types=1);

namespace App\Support\DataFixtures\ORM;

use App\Identity\Infrastructure\Entity\Role;
use App\Identity\Infrastructure\Entity\User;
use App\Organization\Infrastructure\Entity\Department;
use App\Organization\Infrastructure\Entity\FieldOfStudy;
use App\Organization\Infrastructure\Entity\Position;
use App\Organization\Infrastructure\Entity\Team;
use App\Organization\Infrastructure\Entity\TeamMembership;
use App\Shared\Entity\Semester;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class BackgroundAutomationFixture extends AbstractFixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['background-operations'];
    }

    public function load(ObjectManager $manager): void
    {
        $now = new \DateTime('now', new \DateTimeZone('Europe/Oslo'));
        $semester = $this->getCurrentSemester($manager, $now);
        $assistantRole = $this->getOrCreateRole($manager, 'ROLE_USER', 'Background user');

        $department = new Department();
        $department->setName('Background automation department');
        $department->setShortName('BG-AUTO');
        $department->setEmail('background-automation@example.invalid');
        $department->setCity('Background Automation City');
        $department->setAddress('Disposable automation fixture');
        $department->setActive(true);
        $manager->persist($department);

        $fieldOfStudy = new FieldOfStudy();
        $fieldOfStudy->setName('Background automation studies');
        $fieldOfStudy->setShortName('BG-AUTO-STUDY');
        $fieldOfStudy->setDepartment($department);
        $department->addFieldOfStudy($fieldOfStudy);
        $manager->persist($fieldOfStudy);

        $team = new Team();
        $team->setName('Background automation team');
        $team->setEmail('background-automation-team@example.invalid');
        $team->setDepartment($department);
        $team->setDescription('Disposable team for scheduled role automation.');
        $team->setShortDescription('Background automation team');
        $team->setAcceptApplication(true);
        $team->setDeadline((clone $now)->modify('+30 days'));
        $team->setActive(true);
        $department->addTeam($team);
        $manager->persist($team);

        $user = new User();
        $user->setActive(true);
        $user->setEmail('background-automation-user-0032@example.invalid');
        $user->setFirstName('Automation');
        $user->setLastName('User 0032');
        $user->setGender(false);
        $user->setPhone('90000033');
        $user->setUserName('background-automation-user-0032');
        $user->setPassword('background-automation-password-0032');
        $user->setFieldOfStudy($fieldOfStudy);
        $user->setPicturePath('images/defaultProfile.png');
        $user->addRole($assistantRole);
        $manager->persist($user);

        $position = new Position();
        $position->setName('Background automation member');
        $manager->persist($position);

        $membership = new TeamMembership();
        $membership->setUser($user);
        $membership->setTeam($team);
        $membership->setPosition($position);
        $membership->setStartSemester($semester);
        $membership->setEndSemester(null);
        $membership->setIsTeamLeader(false);
        $membership->setIsSuspended(false);
        $manager->persist($membership);

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
}

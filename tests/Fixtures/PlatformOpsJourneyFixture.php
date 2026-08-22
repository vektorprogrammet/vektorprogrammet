<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use App\Identity\Infrastructure\Entity\Role;
use App\Identity\Infrastructure\Entity\User;
use App\Organization\Infrastructure\Entity\Department;
use App\Organization\Infrastructure\Entity\FieldOfStudy;
use App\Shared\Entity\Semester;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class PlatformOpsJourneyFixture extends AbstractFixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['platform-ops'];
    }

    public function load(ObjectManager $manager): void
    {
        $adminRole = $this->role($manager, 'ROLE_ADMIN', 'Platform operations administrator');

        $department = new Department();
        $department->setName('Platform operations department 0032');
        $department->setShortName('PLATFORM-0032');
        $department->setEmail('platform-ops-0032@example.invalid');
        $department->setCity('PlatformCity');
        $department->setAddress('Platform operations fixture');
        $department->setLatitude('63.4305');
        $department->setLongitude('10.3951');
        $department->setActive(true);
        $manager->persist($department);

        $fieldOfStudy = new FieldOfStudy();
        $fieldOfStudy->setName('Platform operations studies 0032');
        $fieldOfStudy->setShortName('PLATFORM-STUDY-0032');
        $fieldOfStudy->setDepartment($department);
        $department->addFieldOfStudy($fieldOfStudy);
        $manager->persist($fieldOfStudy);

        $semester = new Semester();
        $semester->setSemesterTime('Høst');
        $semester->setYear('2033');
        $manager->persist($semester);

        $admin = $this->user(
            'platform-ops-admin-0032',
            'platform-ops-admin-0032@example.invalid',
            'Platform',
            'Administrator',
            $adminRole,
            $fieldOfStudy,
            'platform-ops-admin-password-0032',
        );
        $manager->persist($admin);

        $viewerRole = $this->role($manager, 'ROLE_USER', 'Platform operations viewer');
        $viewer = $this->user(
            'platform-ops-viewer-0032',
            'platform-ops-viewer-0032@example.invalid',
            'Platform',
            'Viewer',
            $viewerRole,
            $fieldOfStudy,
            'platform-ops-viewer-password-0032',
        );
        $manager->persist($viewer);

        $manager->flush();
    }

    private function role(ObjectManager $manager, string $code, string $name): Role
    {
        $role = $manager->getRepository(Role::class)->findOneBy(['role' => $code]);
        if ($role instanceof Role) {
            return $role;
        }

        $role = new Role($code);
        $role->setName($name);
        $manager->persist($role);

        return $role;
    }

    private function user(
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
        $user->setPhone('00000034');
        $user->setUserName($username);
        $user->setPassword($password);
        $user->setFieldOfStudy($fieldOfStudy);
        $user->setPicturePath('images/defaultProfile.png');
        $user->addRole($role);

        return $user;
    }
}

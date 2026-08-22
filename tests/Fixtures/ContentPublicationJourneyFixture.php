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

final class ContentPublicationJourneyFixture extends AbstractFixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['content-publication'];
    }

    public function load(ObjectManager $manager): void
    {
        $role = $this->role($manager, 'ROLE_TEAM_MEMBER', 'Content publication operator');

        $department = new Department();
        $department->setName('Content publication department 0032');
        $department->setShortName('CONTENT-0032');
        $department->setEmail('content-publication-0032@example.invalid');
        $department->setCity('ContentCity');
        $department->setAddress('Content publication fixture');
        $department->setLatitude('63.4305');
        $department->setLongitude('10.3951');
        $department->setActive(true);
        $manager->persist($department);

        $fieldOfStudy = new FieldOfStudy();
        $fieldOfStudy->setName('Content publication studies 0032');
        $fieldOfStudy->setShortName('CONTENT-STUDY-0032');
        $fieldOfStudy->setDepartment($department);
        $department->addFieldOfStudy($fieldOfStudy);
        $manager->persist($fieldOfStudy);

        $semester = new Semester();
        $semester->setSemesterTime('Høst');
        $semester->setYear('2032');
        $manager->persist($semester);

        $operator = $this->user(
            'content-publication-operator-0032',
            'content-publication-operator-0032@example.invalid',
            'Content',
            'Operator',
            $role,
            $fieldOfStudy,
            'content-publication-password-0032',
        );
        $manager->persist($operator);

        $viewerRole = $this->role($manager, 'ROLE_USER', 'Content publication viewer');
        $viewer = $this->user(
            'content-publication-viewer-0032',
            'content-publication-viewer-0032@example.invalid',
            'Content',
            'Viewer',
            $viewerRole,
            $fieldOfStudy,
            'content-publication-viewer-password-0032',
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
        $user->setPhone('00000032');
        $user->setUserName($username);
        $user->setPassword($password);
        $user->setFieldOfStudy($fieldOfStudy);
        $user->setPicturePath('images/defaultProfile.png');
        $user->addRole($role);

        return $user;
    }
}

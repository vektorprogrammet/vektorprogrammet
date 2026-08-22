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

final class SurveyAdminJourneyFixture extends AbstractFixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['survey-admin'];
    }

    public function load(ObjectManager $manager): void
    {
        $role = $this->role($manager, 'ROLE_TEAM_MEMBER', 'Survey administration operator');

        $department = new Department();
        $department->setName('Survey administration department 0032');
        $department->setShortName('SURVEY-0032');
        $department->setEmail('survey-admin-0032@example.invalid');
        $department->setCity('SurveyCity');
        $department->setAddress('Survey administration fixture');
        $department->setLatitude('63.4305');
        $department->setLongitude('10.3951');
        $department->setActive(true);
        $manager->persist($department);

        $fieldOfStudy = new FieldOfStudy();
        $fieldOfStudy->setName('Survey administration studies 0032');
        $fieldOfStudy->setShortName('SURVEY-STUDY-0032');
        $fieldOfStudy->setDepartment($department);
        $department->addFieldOfStudy($fieldOfStudy);
        $manager->persist($fieldOfStudy);

        $now = new \DateTimeImmutable('now');

        $semester = new Semester();
        $semester->setSemesterTime($now->format('n') < 8 ? 'Vår' : 'Høst');
        $semester->setYear($now->format('Y'));
        $manager->persist($semester);

        $operator = $this->user(
            'survey-admin-operator-0032',
            'survey-admin-operator-0032@example.invalid',
            'Survey',
            'Operator',
            $role,
            $fieldOfStudy,
            'survey-admin-password-0032',
        );
        $manager->persist($operator);

        $viewerRole = $this->role($manager, 'ROLE_USER', 'Survey administration viewer');
        $viewer = $this->user(
            'survey-admin-viewer-0032',
            'survey-admin-viewer-0032@example.invalid',
            'Survey',
            'Viewer',
            $viewerRole,
            $fieldOfStudy,
            'survey-admin-viewer-password-0032',
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
        $user->setPhone('00000033');
        $user->setUserName($username);
        $user->setPassword($password);
        $user->setFieldOfStudy($fieldOfStudy);
        $user->setPicturePath('images/defaultProfile.png');
        $user->addRole($role);

        return $user;
    }
}

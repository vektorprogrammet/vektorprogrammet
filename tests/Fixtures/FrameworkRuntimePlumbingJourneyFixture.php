<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use App\Identity\Infrastructure\Entity\Role;
use App\Identity\Infrastructure\Entity\User;
use App\Organization\Infrastructure\Entity\Department;
use App\Organization\Infrastructure\Entity\FieldOfStudy;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class FrameworkRuntimePlumbingJourneyFixture extends AbstractFixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['framework-runtime-plumbing'];
    }

    public function load(ObjectManager $manager): void
    {
        $role = $manager->getRepository(Role::class)->findOneBy(['role' => 'ROLE_USER']);
        if (!$role instanceof Role) {
            $role = new Role('ROLE_USER');
            $role->setName('Framework runtime viewer');
            $manager->persist($role);
        }

        $department = new Department();
        $department->setName('Framework runtime department 0032');
        $department->setShortName('FRAMEWORK-0032');
        $department->setEmail('framework-runtime-0032@example.invalid');
        $department->setCity('FrameworkCity');
        $department->setAddress('Framework runtime fixture');
        $department->setLatitude('63.4305');
        $department->setLongitude('10.3951');
        $department->setActive(true);
        $manager->persist($department);

        $fieldOfStudy = new FieldOfStudy();
        $fieldOfStudy->setName('Framework runtime studies 0032');
        $fieldOfStudy->setShortName('FRAMEWORK-STUDY-0032');
        $fieldOfStudy->setDepartment($department);
        $department->addFieldOfStudy($fieldOfStudy);
        $manager->persist($fieldOfStudy);

        $user = new User();
        $user->setActive(true);
        $user->setEmail('framework-runtime-0032@example.invalid');
        $user->setFirstName('Framework');
        $user->setLastName('Runtime');
        $user->setGender(false);
        $user->setPhone('00000035');
        $user->setUserName('framework-runtime-0032');
        $user->setPassword('framework-runtime-password-0032');
        $user->setFieldOfStudy($fieldOfStudy);
        $user->setPicturePath('images/defaultProfile.png');
        $user->addRole($role);
        $manager->persist($user);

        $manager->flush();
    }
}

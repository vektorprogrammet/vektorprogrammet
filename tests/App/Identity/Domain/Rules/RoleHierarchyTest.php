<?php

namespace App\Tests\App\Identity\Domain\Rules;

use App\Identity\Domain\Roles;
use App\Identity\Domain\Rules\RoleHierarchy;
use App\Identity\Infrastructure\Entity\User;
use PHPUnit\Framework\TestCase;

class RoleHierarchyTest extends TestCase
{
    private RoleHierarchy $hierarchy;

    protected function setUp(): void
    {
        $this->hierarchy = new RoleHierarchy();
    }

    public function testUserIsGrantedChecksAllRolesNotJustFirst(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getRoles')->willReturn([Roles::TEAM_MEMBER, Roles::TEAM_LEADER]);

        $result = $this->hierarchy->userIsGranted($user, Roles::TEAM_LEADER);

        $this->assertTrue($result, 'userIsGranted should return true when any role satisfies the required level');
    }

    public function testUserIsGrantedReturnsFalseWhenNoRoleSuffices(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getRoles')->willReturn([Roles::ASSISTANT]);

        $result = $this->hierarchy->userIsGranted($user, Roles::TEAM_LEADER);

        $this->assertFalse($result);
    }

    public function testUserWithSingleHighRoleIsGranted(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getRoles')->willReturn([Roles::ADMIN]);

        $result = $this->hierarchy->userIsGranted($user, Roles::TEAM_MEMBER);

        $this->assertTrue($result);
    }

    public function testUserWithEmptyRolesIsNotGranted(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getRoles')->willReturn([]);

        $result = $this->hierarchy->userIsGranted($user, Roles::ASSISTANT);

        $this->assertFalse($result);
    }
}

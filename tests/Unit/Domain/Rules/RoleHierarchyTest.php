<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Rules;

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

    public function testValidRoleReturnsTrue(): void
    {
        $this->assertTrue($this->hierarchy->isValidRole(Roles::ASSISTANT));
        $this->assertTrue($this->hierarchy->isValidRole(Roles::TEAM_MEMBER));
        $this->assertTrue($this->hierarchy->isValidRole(Roles::TEAM_LEADER));
        $this->assertTrue($this->hierarchy->isValidRole(Roles::ADMIN));
    }

    public function testValidAliasReturnsTrue(): void
    {
        $this->assertTrue($this->hierarchy->isValidRole(Roles::ALIAS_ASSISTANT));
        $this->assertTrue($this->hierarchy->isValidRole(Roles::ALIAS_TEAM_MEMBER));
        $this->assertTrue($this->hierarchy->isValidRole(Roles::ALIAS_TEAM_LEADER));
        $this->assertTrue($this->hierarchy->isValidRole(Roles::ALIAS_ADMIN));
    }

    public function testInvalidRoleReturnsFalse(): void
    {
        $this->assertFalse($this->hierarchy->isValidRole('ROLE_NONEXISTENT'));
        $this->assertFalse($this->hierarchy->isValidRole(''));
    }

    public function testCannotChangeToAdmin(): void
    {
        $this->assertFalse($this->hierarchy->canChangeToRole(Roles::ADMIN));
        $this->assertFalse($this->hierarchy->canChangeToRole(Roles::ALIAS_ADMIN));
    }

    public function testCanChangeToNonAdminRoles(): void
    {
        $this->assertTrue($this->hierarchy->canChangeToRole(Roles::ASSISTANT));
        $this->assertTrue($this->hierarchy->canChangeToRole(Roles::TEAM_MEMBER));
        $this->assertTrue($this->hierarchy->canChangeToRole(Roles::TEAM_LEADER));
    }

    public function testCannotChangeToInvalidRole(): void
    {
        $this->assertFalse($this->hierarchy->canChangeToRole('ROLE_INVALID'));
    }

    public function testMapAliasToRoleMapsAssistantAlias(): void
    {
        $this->assertSame(Roles::ASSISTANT, $this->hierarchy->mapAliasToRole(Roles::ALIAS_ASSISTANT));
    }

    public function testMapAliasToRoleMapsTeamMemberAlias(): void
    {
        $this->assertSame(Roles::TEAM_MEMBER, $this->hierarchy->mapAliasToRole(Roles::ALIAS_TEAM_MEMBER));
    }

    public function testMapAliasToRoleMapsTeamLeaderAlias(): void
    {
        $this->assertSame(Roles::TEAM_LEADER, $this->hierarchy->mapAliasToRole(Roles::ALIAS_TEAM_LEADER));
    }

    public function testMapAliasToRoleMapsAdminAlias(): void
    {
        $this->assertSame(Roles::ADMIN, $this->hierarchy->mapAliasToRole(Roles::ALIAS_ADMIN));
    }

    public function testMapAliasToRolePassesThroughCanonicalRole(): void
    {
        // Canonical roles are returned as-is
        $this->assertSame(Roles::ASSISTANT, $this->hierarchy->mapAliasToRole(Roles::ASSISTANT));
        $this->assertSame(Roles::ADMIN, $this->hierarchy->mapAliasToRole(Roles::ADMIN));
    }

    public function testMapAliasToRoleThrowsOnInvalidAlias(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->hierarchy->mapAliasToRole('unknown_alias');
    }

    public function testAdminUserIsGrantedAssistantRole(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getRoles')->willReturn([Roles::ADMIN]);

        $this->assertTrue($this->hierarchy->userIsGranted($user, Roles::ASSISTANT));
    }

    public function testAdminUserIsGrantedAdminRole(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getRoles')->willReturn([Roles::ADMIN]);

        $this->assertTrue($this->hierarchy->userIsGranted($user, Roles::ADMIN));
    }

    public function testAssistantUserIsNotGrantedAdminRole(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getRoles')->willReturn([Roles::ASSISTANT]);

        $this->assertFalse($this->hierarchy->userIsGranted($user, Roles::ADMIN));
    }

    public function testAssistantUserIsGrantedAssistantRole(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getRoles')->willReturn([Roles::ASSISTANT]);

        $this->assertTrue($this->hierarchy->userIsGranted($user, Roles::ASSISTANT));
    }

    public function testUserWithNoRolesReturnsFalse(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getRoles')->willReturn([]);

        $this->assertFalse($this->hierarchy->userIsGranted($user, Roles::ASSISTANT));
    }

    public function testUserWithUnknownRoleReturnsFalse(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getRoles')->willReturn(['ROLE_UNKNOWN']);

        $this->assertFalse($this->hierarchy->userIsGranted($user, Roles::ASSISTANT));
    }

    public function testTeamMemberIsNotGrantedTeamLeader(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getRoles')->willReturn([Roles::TEAM_MEMBER]);

        $this->assertFalse($this->hierarchy->userIsGranted($user, Roles::TEAM_LEADER));
    }

    public function testTeamLeaderIsGrantedTeamMember(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getRoles')->willReturn([Roles::TEAM_LEADER]);

        $this->assertTrue($this->hierarchy->userIsGranted($user, Roles::TEAM_MEMBER));
    }
}

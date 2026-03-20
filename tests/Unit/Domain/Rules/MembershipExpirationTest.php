<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Rules;

use App\Organization\Domain\Rules\MembershipExpiration;
use App\Organization\Infrastructure\Entity\TeamMembership;
use App\Shared\Entity\Semester;
use PHPUnit\Framework\TestCase;

class MembershipExpirationTest extends TestCase
{
    private MembershipExpiration $expiration;

    protected function setUp(): void
    {
        $this->expiration = new MembershipExpiration();
    }

    public function testNullEndSemesterIsNotExpired(): void
    {
        $membership = $this->createMock(TeamMembership::class);
        $membership->method('getEndSemester')->willReturn(null);

        $currentDate = new \DateTime('2026-01-01');

        $this->assertFalse($this->expiration->isExpired($membership, $currentDate));
    }

    public function testEndDateBeforeCurrentDateIsExpired(): void
    {
        $endSemester = $this->createMock(Semester::class);
        $endSemester->method('getEndDate')->willReturn(new \DateTime('2025-06-01'));

        $membership = $this->createMock(TeamMembership::class);
        $membership->method('getEndSemester')->willReturn($endSemester);

        $currentDate = new \DateTime('2026-01-01');

        $this->assertTrue($this->expiration->isExpired($membership, $currentDate));
    }

    public function testEndDateAfterCurrentDateIsNotExpired(): void
    {
        $endSemester = $this->createMock(Semester::class);
        $endSemester->method('getEndDate')->willReturn(new \DateTime('2027-01-01'));

        $membership = $this->createMock(TeamMembership::class);
        $membership->method('getEndSemester')->willReturn($endSemester);

        $currentDate = new \DateTime('2026-01-01');

        $this->assertFalse($this->expiration->isExpired($membership, $currentDate));
    }

    public function testEndDateEqualToCurrentDateIsExpired(): void
    {
        // The rule uses <=, so equal dates count as expired
        $sameDate = new \DateTime('2026-01-01');

        $endSemester = $this->createMock(Semester::class);
        $endSemester->method('getEndDate')->willReturn($sameDate);

        $membership = $this->createMock(TeamMembership::class);
        $membership->method('getEndSemester')->willReturn($endSemester);

        $this->assertTrue($this->expiration->isExpired($membership, $sameDate));
    }
}

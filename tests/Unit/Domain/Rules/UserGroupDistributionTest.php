<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Rules;

use App\Organization\Domain\Rules\UserGroupDistribution;
use PHPUnit\Framework\TestCase;

class UserGroupDistributionTest extends TestCase
{
    private UserGroupDistribution $distribution;

    protected function setUp(): void
    {
        $this->distribution = new UserGroupDistribution();
    }

    public function testEvenDistributionProducesEqualGroups(): void
    {
        $users = range(1, 12);
        $groups = $this->distribution->distribute($users, 3);

        $this->assertCount(3, $groups);
        $this->assertCount(4, $groups[0]);
        $this->assertCount(4, $groups[1]);
        $this->assertCount(4, $groups[2]);
    }

    public function testRemainderDistributedAcrossFirstGroups(): void
    {
        $users = range(1, 13);
        $groups = $this->distribution->distribute($users, 3);

        $this->assertCount(3, $groups);

        // Total users must be preserved
        $total = array_sum(array_map('count', $groups));
        $this->assertSame(13, $total);

        // Each group must have at least 4 users (floor(13/3) = 4)
        foreach ($groups as $group) {
            $this->assertGreaterThanOrEqual(4, count($group));
        }
    }

    public function testAllUsersArePreservedAfterDistribution(): void
    {
        $users = range(1, 17);
        $groups = $this->distribution->distribute($users, 4);

        $distributed = array_merge(...$groups);
        sort($distributed);

        $this->assertSame($users, $distributed);
    }

    public function testGroupCountLessThanOnThrowsInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->distribution->distribute([1, 2, 3], 0);
    }

    public function testNegativeGroupCountThrowsInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->distribution->distribute([1, 2, 3], -1);
    }

    public function testTooFewUsersForRequestedGroupsThrowsUnexpectedValueException(): void
    {
        $this->expectException(\UnexpectedValueException::class);

        // 1 user into 3 groups: groupSize = floor(1/3) = 0, triggers exception
        $this->distribution->distribute([1], 3);
    }

    public function testSingleGroupReturnsAllUsers(): void
    {
        $users = range(1, 5);
        $groups = $this->distribution->distribute($users, 1);

        $this->assertCount(1, $groups);
        $this->assertCount(5, $groups[0]);
    }

    public function testCorrectNumberOfGroupsCreated(): void
    {
        $users = range(1, 20);
        $groups = $this->distribution->distribute($users, 4);

        $this->assertCount(4, $groups);
    }
}

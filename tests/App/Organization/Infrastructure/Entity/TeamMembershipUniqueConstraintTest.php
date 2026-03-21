<?php

namespace App\Tests\App\Organization\Infrastructure\Entity;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TeamMembershipUniqueConstraintTest extends KernelTestCase
{
    public function testDuplicateUserTeamSemesterThrowsUniqueConstraintViolation(): void
    {
        self::bootKernel();
        $em = self::getContainer()->get('doctrine.orm.entity_manager');
        $conn = $em->getConnection();

        // Insert a semester row directly
        $conn->executeStatement(
            'INSERT INTO semester (semester_time, year) VALUES (?, ?)',
            ['Vår', '2098']
        );
        $semesterId = $conn->lastInsertId();

        // Insert a team row with minimal data (null team_id for department is ok in SQLite)
        $conn->executeStatement(
            'INSERT INTO team (name, active) VALUES (?, ?)',
            ['TestTeamUQ-' . uniqid(), 1]
        );
        $teamId = $conn->lastInsertId();

        // Insert first membership
        $conn->executeStatement(
            'INSERT INTO team_membership (user_id, team_id, start_semester_id, is_team_leader, is_suspended) VALUES (?, ?, ?, ?, ?)',
            [999999, $teamId, $semesterId, 0, 0]
        );

        // Try to insert duplicate (same user, team, semester)
        $this->expectException(UniqueConstraintViolationException::class);

        $conn->executeStatement(
            'INSERT INTO team_membership (user_id, team_id, start_semester_id, is_team_leader, is_suspended) VALUES (?, ?, ?, ?, ?)',
            [999999, $teamId, $semesterId, 0, 0]
        );
    }
}

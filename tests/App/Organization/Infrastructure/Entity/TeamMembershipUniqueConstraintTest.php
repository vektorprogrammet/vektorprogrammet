<?php

namespace App\Tests\App\Organization\Infrastructure\Entity;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * A person joins a team once per semester PER POSITION.
 *
 * Position is part of the key on purpose: a production scan of
 * (user_id, team_id, start_semester_id) alone found 13 colliding groups, 6 of which
 * differ only by position_id and are valid data -- one person holding two positions
 * in the same team and semester. Dropping position from this key would make that
 * valid data unrepresentable.
 */
class TeamMembershipUniqueConstraintTest extends KernelTestCase
{
    public function testDuplicateUserTeamSemesterPositionThrowsUniqueConstraintViolation(): void
    {
        self::bootKernel();
        $em = self::getContainer()->get('doctrine.orm.entity_manager');
        $conn = $em->getConnection();

        $conn->executeStatement('INSERT INTO semester (semester_time, year) VALUES (?, ?)', ['Vår', '2098']);
        $semesterId = $conn->lastInsertId();

        $conn->executeStatement('INSERT INTO team (name, active) VALUES (?, ?)', ['TestTeamUQ-'.uniqid(), 1]);
        $teamId = $conn->lastInsertId();

        $conn->executeStatement('INSERT INTO position (name) VALUES (?)', ['TestPositionUQ-'.uniqid()]);
        $positionId = $conn->lastInsertId();

        $conn->executeStatement(
            'INSERT INTO team_membership (user_id, team_id, start_semester_id, position_id, is_team_leader, is_suspended) VALUES (?, ?, ?, ?, ?, ?)',
            [999999, $teamId, $semesterId, $positionId, 0, 0]
        );

        $this->expectException(UniqueConstraintViolationException::class);

        $conn->executeStatement(
            'INSERT INTO team_membership (user_id, team_id, start_semester_id, position_id, is_team_leader, is_suspended) VALUES (?, ?, ?, ?, ?, ?)',
            [999999, $teamId, $semesterId, $positionId, 0, 0]
        );
    }

    public function testSameUserTeamAndSemesterInAnotherPositionIsAllowed(): void
    {
        self::bootKernel();
        $em = self::getContainer()->get('doctrine.orm.entity_manager');
        $conn = $em->getConnection();

        $conn->executeStatement('INSERT INTO semester (semester_time, year) VALUES (?, ?)', ['Høst', '2097']);
        $semesterId = $conn->lastInsertId();

        $conn->executeStatement('INSERT INTO team (name, active) VALUES (?, ?)', ['TestTeamTwoPos-'.uniqid(), 1]);
        $teamId = $conn->lastInsertId();

        $conn->executeStatement('INSERT INTO position (name) VALUES (?)', ['TestPositionA-'.uniqid()]);
        $firstPositionId = $conn->lastInsertId();

        $conn->executeStatement('INSERT INTO position (name) VALUES (?)', ['TestPositionB-'.uniqid()]);
        $secondPositionId = $conn->lastInsertId();

        $insert = 'INSERT INTO team_membership (user_id, team_id, start_semester_id, position_id, is_team_leader, is_suspended) VALUES (?, ?, ?, ?, ?, ?)';
        $conn->executeStatement($insert, [999998, $teamId, $semesterId, $firstPositionId, 0, 0]);
        $conn->executeStatement($insert, [999998, $teamId, $semesterId, $secondPositionId, 0, 0]);

        $held = $conn->fetchOne(
            'SELECT COUNT(*) FROM team_membership WHERE user_id = ? AND team_id = ? AND start_semester_id = ?',
            [999998, $teamId, $semesterId]
        );

        $this->assertSame(
            2,
            (int) $held,
            'One person must be able to hold two positions in the same team and semester'
        );
    }
}

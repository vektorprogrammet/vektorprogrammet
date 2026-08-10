<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Guard-parity batch items A1, A2, A6 and A7 declared four uniqueness invariants as ORM
 * metadata only. Production is migration-driven, so none of them exist there.
 *
 * A scan of a production backup (2024-08-22, 2923 users) says only two of the four can be
 * enforced today, so only those two are in this migration:
 *
 *   SHIPPED HERE
 *   A6  receipt(visual_id)                                     0 duplicates, 0 nulls
 *   A1  school_capacity(school_id, semester_id, department_id) 0 duplicates
 *       -- the audit asked for (school_id, semester_id). That key is MIS-MODELED: it has
 *          3 colliding groups in production because a school legitimately has capacity in
 *          one semester under two departments. The entity was corrected to include
 *          department_id, which is clean, and this migration follows the corrected entity.
 *
 *   NOT SHIPPED -- blocked on operator data work
 *   A2  assistant_history(user_id, school_id, semester_id, bolk)
 *       -- also mis-modeled, and blocked on a data-SHAPE migration rather than a cleanup:
 *          a semester has two teaching blocks and an assistant is placed once per bolk, so
 *          bolk belongs in the key. 332 production rows still store "both bolks" as a
 *          single 'Bolk 1, Bolk 2' row and have to be split into two rows first.
 *   A7  team_membership(user_id, team_id, start_semester_id, position_id)
 *                                                              7 duplicate groups remain
 *       -- also mis-modeled as stated: of 13 collisions on the audit's key, 6 differ only
 *          by position_id and are valid data. The entity was corrected to include
 *          position_id; the residual 7 are true duplicates.
 *
 * See docs/migrations-blocked-unique-constraints.md for the cleanup queries and the
 * follow-up migration those two need.
 *
 * DATA SAFETY
 * ===========
 * MySQL applies DDL non-transactionally, so a duplicate found while creating the second
 * index would leave the database half-migrated. preUp() re-runs the duplicate scan against
 * the live database and refuses to start unless both invariants hold -- the production scan
 * above is a point-in-time reading of a backup, not a promise about the database this runs
 * on. Failing before any DDL is the only state that is safe to retry.
 */
final class Version20260810002046 extends AbstractMigration
{
    /**
     * Each entry mirrors exactly one ORM declaration, including the index name Doctrine
     * derives from it, so `doctrine:migrations:diff` stays empty for these two tables.
     *
     * @var array<string, array{table: string, index: string, columns: list<string>, source: string}>
     */
    private const CONSTRAINTS = [
        'A1' => [
            'table' => 'school_capacity',
            'index' => 'unique_school_semester_department',
            'columns' => ['school_id', 'semester_id', 'department_id'],
            'source' => 'App\Scheduling\Infrastructure\Entity\SchoolCapacity',
        ],
        'A6' => [
            'table' => 'receipt',
            'index' => 'UNIQ_5399B64560D949C1',
            'columns' => ['visual_id'],
            'source' => 'App\Operations\Infrastructure\Entity\Receipt::$visualId',
        ],
    ];

    public function getDescription(): string
    {
        return 'Add unique indexes for receipt visual id (A6) and school capacity per school+semester+department (A1). A2 and A7 are blocked on data cleanup.';
    }

    /**
     * Refuse the whole migration if either invariant is already violated, so no index is
     * created against data that cannot satisfy it.
     */
    public function preUp(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform,
            'Migration can only be executed safely on \'mysql\'.'
        );

        $violations = [];
        foreach (self::CONSTRAINTS as $item => $constraint) {
            $duplicates = (int) $this->connection->fetchOne($this->duplicateCountSql($constraint));
            if ($duplicates > 0) {
                $violations[] = sprintf(
                    '%s: %s(%s) has %d duplicate group(s). Scan: %s',
                    $item,
                    $constraint['table'],
                    implode(', ', $constraint['columns']),
                    $duplicates,
                    $this->duplicateGroupSql($constraint)
                );
            }
        }

        $this->abortIf(
            [] !== $violations,
            "Existing rows violate the uniqueness invariants; no index was created. Resolve the duplicates (or the constraint) first:\n- ".implode("\n- ", $violations)
        );
    }

    public function up(Schema $schema): void
    {
        foreach (self::CONSTRAINTS as $constraint) {
            $this->addSql(sprintf(
                'CREATE UNIQUE INDEX %s ON %s (%s)',
                $constraint['index'],
                $constraint['table'],
                implode(', ', $constraint['columns'])
            ));
        }
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform,
            'Migration can only be executed safely on \'mysql\'.'
        );

        foreach (self::CONSTRAINTS as $constraint) {
            $this->addSql(sprintf('DROP INDEX %s ON %s', $constraint['index'], $constraint['table']));
        }
    }

    /**
     * @param array{table: string, index: string, columns: list<string>, source: string} $constraint
     */
    private function duplicateCountSql(array $constraint): string
    {
        return sprintf('SELECT COUNT(*) FROM (%s) AS duplicate_groups', $this->duplicateGroupSql($constraint));
    }

    /**
     * The operator-runnable scan: one row per colliding key. NULL keys are excluded because
     * MySQL treats NULL as distinct in a UNIQUE index, so rows with a NULL part can never
     * collide.
     *
     * @param array{table: string, index: string, columns: list<string>, source: string} $constraint
     */
    private function duplicateGroupSql(array $constraint): string
    {
        $columns = implode(', ', $constraint['columns']);
        $notNull = implode(' AND ', array_map(
            static fn (string $column): string => $column.' IS NOT NULL',
            $constraint['columns']
        ));

        return sprintf(
            'SELECT %s, COUNT(*) AS n FROM %s WHERE %s GROUP BY %s HAVING COUNT(*) > 1',
            $columns,
            $constraint['table'],
            $notNull,
            $columns
        );
    }
}

<?php

namespace Tests\AppBundle\Service;

use AppBundle\Service\MigrationMetadataUpgrader;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use PHPUnit\Framework\TestCase;

final class MigrationMetadataUpgraderTest extends TestCase
{
    private Connection $connection;
    private MigrationMetadataUpgrader $upgrader;

    protected function setUp(): void
    {
        $this->connection = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);
        $this->connection->executeStatement('CREATE TABLE migration_versions (version VARCHAR(191) PRIMARY KEY, executed_at VARCHAR(30))');
        $this->upgrader = new MigrationMetadataUpgrader($this->connection, dirname(__DIR__, 3));
    }

    protected function tearDown(): void
    {
        $this->connection->close();
    }

    public function testPreviewDoesNotChangeRecordedHistory(): void
    {
        $this->connection->insert('migration_versions', ['version' => '20170913114609', 'executed_at' => '2021-01-01']);
        $this->assertSame(['20170913114609' => 'Application\\Migrations\\Version20170913114609'], $this->upgrader->plan());
        $this->assertSame('20170913114609', $this->connection->fetchOne('SELECT version FROM migration_versions'));
    }

    public function testConversionPreservesExecutionDatesAndIsIdempotent(): void
    {
        $this->connection->insert('migration_versions', ['version' => '20170913114609', 'executed_at' => '2021-01-01']);
        $this->connection->insert('migration_versions', ['version' => 'CreateExecutiveBoard', 'executed_at' => '2021-02-01']);
        $this->assertSame(2, $this->upgrader->apply());
        $this->assertSame('2021-01-01', $this->connection->fetchOne('SELECT executed_at FROM migration_versions WHERE version = ?', ['Application\\Migrations\\Version20170913114609']));
        $this->assertSame([], $this->upgrader->plan());
        $this->assertSame(0, $this->upgrader->apply());
    }

    public function testUnknownVersionAbortsWithoutPartialConversion(): void
    {
        $this->connection->insert('migration_versions', ['version' => '20170913114609']);
        $this->connection->insert('migration_versions', ['version' => 'missing_migration']);
        try {
            $this->upgrader->apply();
            $this->fail('Unknown migration identifiers must require review.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('No migration class', $exception->getMessage());
        }
        $this->assertSame(1, (int) $this->connection->fetchOne('SELECT COUNT(*) FROM migration_versions WHERE version = ?', ['20170913114609']));
    }

    public function testDuplicateDestinationsAreRejected(): void
    {
        $this->connection->insert('migration_versions', ['version' => '20170913114609']);
        $this->connection->insert('migration_versions', ['version' => 'Application\\Migrations\\Version20170913114609']);
        $this->expectException(\RuntimeException::class);
        $this->upgrader->apply();
    }
}

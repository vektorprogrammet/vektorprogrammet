<?php

namespace AppBundle\Service;

use Doctrine\DBAL\Connection;

/** Converts Doctrine Migrations 1 version identifiers without executing migrations. */
final class MigrationMetadataUpgrader
{
    public function __construct(private Connection $connection, private string $projectDir)
    {
    }

    public function plan(): array
    {
        if (!$this->connection->createSchemaManager()->tablesExist(['migration_versions'])) {
            return [];
        }

        $changes = [];
        $destinations = [];
        foreach ($this->connection->fetchFirstColumn('SELECT version FROM migration_versions') as $version) {
            $version = (string) $version;
            if (str_starts_with($version, 'Application\\Migrations\\')) {
                $qualified = $version;
            } else {
                if (!preg_match('/^[A-Za-z0-9_]+$/D', $version)) {
                    throw new \RuntimeException('Unrecognized migration identifier: '.$version);
                }
                $class = str_starts_with($version, 'Version') ? $version : 'Version'.$version;
                if (!is_file($this->projectDir.'/app/DoctrineMigrations/'.$class.'.php')) {
                    throw new \RuntimeException('No migration class found for recorded version: '.$version);
                }
                $qualified = 'Application\\Migrations\\'.$class;
                $changes[$version] = $qualified;
            }
            if (isset($destinations[$qualified])) {
                throw new \RuntimeException('Multiple recorded versions refer to '.$qualified.'. Review the migration history before proceeding.');
            }
            $destinations[$qualified] = true;
        }

        return $changes;
    }

    public function apply(): int
    {
        return $this->connection->transactional(function (): int {
            $changes = $this->plan();
            foreach ($changes as $old => $new) {
                if ($this->connection->update('migration_versions', ['version' => $new], ['version' => (string) $old]) !== 1) {
                    throw new \RuntimeException('Migration history changed during conversion.');
                }
            }
            return count($changes);
        });
    }
}

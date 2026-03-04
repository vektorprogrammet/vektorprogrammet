<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Dotenv\Dotenv;

class TestDataManager
{
    public static string $testDir = __DIR__.'/../var/data/';

    private function __construct()
    {
    }

    public static function getToken(): string
    {
        return getenv('TEST_TOKEN') ?: '';
    }

    public static function getDbFile(): string
    {
        return 'test'.self::getToken().'.db';
    }

    public static function getBackupFile(): string
    {
        return 'test'.self::getToken().'.db.bk';
    }

    public static function executeCommand(Application $application, string $command, array $options = []): void
    {
        $options['--env'] = 'test';
        $options['--quiet'] = true;
        $options = array_merge(['command' => $command], $options);
        $arrayInput = new ArrayInput($options);
        $arrayInput->setInteractive(false);
        $application->run($arrayInput);
    }

    public static function deleteDatabase(): void
    {
        foreach ([self::getDbFile(), self::getBackupFile()] as $file) {
            $path = self::$testDir.$file;
            if (file_exists($path)) {
                unlink($path);
            }
            if (file_exists($path.'-journal')) {
                unlink($path.'-journal');
            }
        }
    }

    public static function backupDatabase(): void
    {
        copy(self::$testDir.self::getDbFile(), self::$testDir.self::getBackupFile());
    }

    public static function restoreDatabase(): void
    {
        $src = self::$testDir.self::getBackupFile();
        $dst = self::$testDir.self::getDbFile();
        if (!file_exists($src)) {
            return;
        }
        copy($src, $dst);
    }
}

// Skip DB setup if backup already exists (fast path for repeated runs).
// To rebuild: delete var/data/test.db.bk, or run:
//   bin/console doctrine:schema:drop --force --env=test
//   bin/console doctrine:schema:create --env=test
//   bin/console doctrine:fixtures:load --env=test --no-interaction
//   cp var/data/test.db var/data/test.db.bk
$backupPath = TestDataManager::$testDir.TestDataManager::getBackupFile();
if (!file_exists($backupPath)) {
    $envFile = dirname(__DIR__).'/.env';
    if (is_file($envFile)) {
        (new Dotenv())->bootEnv($envFile);
    } elseif (is_file($envFile.'.test')) {
        (new Dotenv())->loadEnv($envFile.'.test', 'APP_ENV', 'test');
    }

    $kernel = new Kernel('test', true);
    $kernel->boot();

    $application = new Application($kernel);
    $application->setAutoExit(false);

    TestDataManager::deleteDatabase();
    TestDataManager::executeCommand($application, 'doctrine:schema:create');
    TestDataManager::executeCommand($application, 'doctrine:fixtures:load');
    TestDataManager::backupDatabase();

    $kernel->shutdown();
}

<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Dotenv\Dotenv;

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
TestDataManager::executeCommand($application, "doctrine:schema:create");
TestDataManager::executeCommand($application, "doctrine:fixtures:load");
TestDataManager::backupDatabase();

class TestDataManager
{

	static $testDir = __DIR__ . '/../var/data/';

    /**
     * TestDataManager constructor.
     */
    private function __construct()
    {
    }

    public static function getToken(): string
    {
        return getenv('TEST_TOKEN') ?: '';
    }

    public static function getDbFile(): string
    {
        return 'test' . self::getToken() . '.db';
    }

    public static function getBackupFile(): string
    {
        return 'test' . self::getToken() . '.db.bk';
    }

    public static function executeCommand(Application $application, $command, Array $options = array()) {
        $options["--env"] = "test";
        $options["--quiet"] = true;
        $options = array_merge(array('command' => $command), $options);
        $arrayInput = new ArrayInput($options);
        $arrayInput->setInteractive(false);
        $application->run($arrayInput);
    }

    public static function deleteDatabase() {
        foreach(array(self::getDbFile(), self::getBackupFile()) AS $file){
            if(file_exists(self::$testDir . $file)){
                unlink(self::$testDir . $file);
            }
        }
    }

    public static function backupDatabase() {
        copy(self::$testDir . self::getDbFile(), self::$testDir . self::getBackupFile());
    }

    public static function restoreDatabase() {
        copy(self::$testDir . self::getBackupFile(), self::$testDir . self::getDbFile());
    }
}

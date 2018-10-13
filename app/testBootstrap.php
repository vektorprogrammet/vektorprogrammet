<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

$kernel = new AppKernel('test', true); // create a "test" kernel
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

    public static function executeCommand(Application $application, $command, Array $options = array()) {
        $options["--env"] = "test";
        $options["--quiet"] = true;
        $options = array_merge(array('command' => $command), $options);
        $arrayInput = new ArrayInput($options);
        $arrayInput->setInteractive(false);
        $application->run($arrayInput);
    }

    public static function deleteDatabase() {
        foreach(array('test.db','test.db.bk') AS $file){
            if(file_exists(self::$testDir . $file)){
                unlink(self::$testDir . $file);
            }
        }
    }

    public static function backupDatabase() {
        copy(self::$testDir.'test.db', self::$testDir.'test.db.bk');
    }

    public static function restoreDatabase() {
        copy(self::$testDir.'test.db.bk', self::$testDir.'test.db');
    }
}

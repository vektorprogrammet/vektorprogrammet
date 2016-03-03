<?php

require_once __DIR__ . '/bootstrap.php.cache';

require_once __DIR__ . '/AppKernel.php';

/*use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

$kernel = new AppKernel('test', true); // create a "test" kernel
$kernel->boot();

$application = new Application($kernel);
$application->setAutoExit(false);

deleteDatabase();
executeCommand($application, "doctrine:schema:create");
executeCommand($application, "doctrine:fixtures:load");

backupDatabase();

function executeCommand($application, $command, Array $options = array()) {
    $options["--env"] = "test";
    $options["--quiet"] = true;
    $options = array_merge($options, array('command' => $command));

    $application->run(new ArrayInput($options));
}

function deleteDatabase() {
    $folder = __DIR__ . '/cache/test/';
    foreach(array('test.db','test.db.bk') AS $file){
        if(file_exists($folder . $file)){
            unlink($folder . $file);
        }
    }
}

function backupDatabase() {
    copy(__DIR__ . '/cache/test/test.db', __DIR__ . '/cache/test/test.db.bk');
}

function restoreDatabase() {
    copy(__DIR__ . '/cache/test/test.db.bk', __DIR__ . '/cache/test/test.db');
}*/
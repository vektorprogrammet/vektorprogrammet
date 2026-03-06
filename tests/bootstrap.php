<?php

require __DIR__.'/../vendor/autoload.php';

use DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Dotenv\Dotenv;

$envFile = dirname(__DIR__).'/.env';
if (is_file($envFile)) {
    (new Dotenv())->bootEnv($envFile);
} elseif (is_file($envFile.'.test')) {
    (new Dotenv())->loadEnv($envFile.'.test', 'APP_ENV', 'test');
}

// Enable DAMA static connections BEFORE kernel boot so the in-memory
// SQLite DB created here is the same one used by all tests.
StaticDriver::setKeepStaticConnections(true);

$kernel = new Kernel('test', true);
$kernel->boot();

$application = new Application($kernel);
$application->setAutoExit(false);

$options = ['--env' => 'test', '--quiet' => true];
$application->run(new ArrayInput(array_merge(['command' => 'doctrine:schema:create'], $options)));
$application->run(new ArrayInput(array_merge(['command' => 'doctrine:fixtures:load', '--no-interaction' => true], $options)));

// Commit the transaction that DAMA auto-started in connect().
// This persists schema + fixtures in the in-memory DB.
// DAMA's per-test begin/rollback cycle then works normally.
StaticDriver::commit();

$kernel->shutdown();

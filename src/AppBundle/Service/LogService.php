<?php

namespace AppBundle\Service;

use Monolog\Logger;
use Psr\Log\LoggerInterface;

class LogService implements LoggerInterface
{
    private $monoLogger;
    private $slackMessenger;

    /**
     * LogService constructor.
     *
     * @param Logger         $monoLogger
     * @param SlackMessenger $slackMessenger
     */
    public function __construct(Logger $monoLogger, SlackMessenger $slackMessenger)
    {
        $this->monoLogger = $monoLogger;
        $this->slackMessenger = $slackMessenger;
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array  $context
     */
    public function emergency($message, array $context = array())
    {
        $this->monoLogger->emergency($message, $context);
        $this->slackMessenger->log('EMERGENCY: '.$message);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array  $context
     */
    public function alert($message, array $context = array())
    {
        $this->monoLogger->alert($message, $context);
        $this->slackMessenger->log('ALERT: '.$message);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array  $context
     */
    public function critical($message, array $context = array())
    {
        $this->monoLogger->critical($message, $context);
        $this->slackMessenger->log('CRITICAL: '.$message);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array  $context
     */
    public function error($message, array $context = array())
    {
        $this->monoLogger->error($message, $context);
        $this->slackMessenger->log('ERROR: '.$message);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array  $context
     */
    public function warning($message, array $context = array())
    {
        $this->monoLogger->warning($message, $context);
        $this->slackMessenger->log('WARNING: '.$message);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array  $context
     */
    public function notice($message, array $context = array())
    {
        $this->monoLogger->notice($message, $context);
        $this->slackMessenger->log('NOTICE: '.$message);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array  $context
     */
    public function info($message, array $context = array())
    {
        $this->monoLogger->info($message, $context);
        $this->slackMessenger->log('INFO: '.$message);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array  $context
     */
    public function debug($message, array $context = array())
    {
        $this->monoLogger->debug($message, $context);
        $this->slackMessenger->log('DEBUG: '.$message);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     */
    public function log($level, $message, array $context = array())
    {
        $this->monoLogger->log($message, $context);
        $this->slackMessenger->log('LOG: '.$message);
    }
}

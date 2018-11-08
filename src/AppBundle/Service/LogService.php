<?php

namespace AppBundle\Service;

use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class LogService implements LoggerInterface
{
    private $monoLogger;
    private $slackMessenger;
    private $userService;
    private $requestStack;
    /**
     * @var string
     */
    private $env;

    /**
     * LogService constructor.
     *
     * @param Logger $monoLogger
     * @param SlackMessenger $slackMessenger
     * @param UserService $userService
     * @param RequestStack $requestStack
     * @param string $env
     */
    public function __construct(Logger $monoLogger, SlackMessenger $slackMessenger, UserService $userService, RequestStack $requestStack, string $env)
    {
        $this->monoLogger = $monoLogger;
        $this->slackMessenger = $slackMessenger;
        $this->userService = $userService;
        $this->requestStack = $requestStack;
        $this->env = $env;
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
        $this->log('EMERGENCY', $message, $context);
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
        $this->log('ALERT', $message, $context);
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
        $this->log('CRITICAL', $message, $context);
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
        $this->log('ERROR', $message, $context);
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
        $this->log('WARNING', $message, $context);
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
        $this->log('NOTICE', $message, $context);
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
        $this->log('INFO', $message, $context);
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
        $this->log('DEBUG', $message, $context);
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
        $this->monoLogger->log(200, $message, $context);
        $this->slackMessenger->log("", $this->createAttachmentData($level, $message, $context));
    }

    private function createAttachmentData($level, $message, array $data)
    {
        $request = $this->requestStack->getMasterRequest();
        $method = $request ? $request->getMethod() : '';
        $path = $request ? $request->getPathInfo() : '???';
        if ('staging' === $this->env) {
            $path = $request ? $request->getUri() : '???';
        }

        $default = [
            'color' => $this->getLogColor($level),
            'author_name' => $this->userService->getCurrentUserNameAndDepartment(),
            'author_icon' => $this->userService->getCurrentProfilePicture(),
            'text' => "$message",
            'footer' => "$level - $method $path"
        ];

        return array_merge($default, $data);
    }

    private function getLogColor($level)
    {
        switch ($level) {
            case 'INFO':
                return '#6fceee';
            case 'WARNING':
                return '#fd7e14';
            case 'CRITICAL':
            case 'ERROR':
            case 'ALERT':
            case 'EMERGENCY':
                return '#dc3545';
            default:
                return '#007bff';
        }
    }
}

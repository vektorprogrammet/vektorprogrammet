<?php

declare(strict_types=1);

namespace App\Support\Infrastructure;

use App\Identity\Infrastructure\UserService;
use App\Support\Infrastructure\Slack\SlackMessenger;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class LogService implements LoggerInterface
{
    public function __construct(
        private readonly Logger $monoLogger,
        private readonly SlackMessenger $slackMessenger,
        private readonly UserService $userService,
        private readonly RequestStack $requestStack,
        private readonly string $env,
    ) {
    }

    public function emergency(\Stringable|string $message, array $context = []): void
    {
        $this->monoLogger->emergency($message, $context);
        $this->log('EMERGENCY', $message, $context);
    }

    public function alert(\Stringable|string $message, array $context = []): void
    {
        $this->monoLogger->alert($message, $context);
        $this->log('ALERT', $message, $context);
    }

    public function critical(\Stringable|string $message, array $context = []): void
    {
        $this->monoLogger->critical($message, $context);
        $this->log('CRITICAL', $message, $context);
    }

    public function error(\Stringable|string $message, array $context = []): void
    {
        $this->monoLogger->error($message, $context);
        $this->log('ERROR', $message, $context);
    }

    public function warning(\Stringable|string $message, array $context = []): void
    {
        $this->monoLogger->warning($message, $context);
        $this->log('WARNING', $message, $context);
    }

    public function notice(\Stringable|string $message, array $context = []): void
    {
        $this->monoLogger->notice($message, $context);
        $this->log('NOTICE', $message, $context);
    }

    public function info(\Stringable|string $message, array $context = []): void
    {
        $this->monoLogger->info($message, $context);
        $this->log('INFO', $message, $context);
    }

    public function debug(\Stringable|string $message, array $context = []): void
    {
        $this->monoLogger->debug($message, $context);
        $this->log('DEBUG', $message, $context);
    }

    public function log($level, \Stringable|string $message, array $context = []): void
    {
        $this->monoLogger->log('info', $message, $context);
        $this->slackMessenger->log('', $this->createAttachmentData($level, $message, $context));
    }

    private function createAttachmentData($level, $message, array $data)
    {
        $request = $this->requestStack->getMainRequest();
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
            'footer' => "$level - $method $path",
        ];

        return array_merge($default, $data);
    }

    private function getLogColor($level)
    {
        return match ($level) {
            'INFO' => '#6fceee',
            'WARNING' => '#fd7e14',
            'CRITICAL', 'ERROR', 'ALERT', 'EMERGENCY' => '#dc3545',
            default => '#007bff',
        };
    }
}

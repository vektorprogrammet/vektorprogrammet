<?php

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * ExceptionListener constructor.
     */
    public function __construct(private readonly LoggerInterface $logger, private readonly LoggerInterface $fileLogger)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => [
                ['logException', 0],
            ],
        ];
    }

    public function logException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        if ($exception instanceof HttpException) {
            $this->logHttpException($exception);

            return;
        }
        $errorMsg = "```\n".
                    "{$exception->getMessage()}\n".
                    "```\n".
                    "in {$exception->getFile()} (line {$exception->getLine()})\n".
                    '@channel';

        $this->logger->critical($errorMsg);

        $this->fileLogger->critical(
            "File: {$exception->getFile()}\n".
            "Line: {$exception->getLine()}\n".
            "Message: {$exception->getMessage()}\n".
            $exception->getTraceAsString()
        );
    }

    private function logHttpException(HttpException $exception)
    {
        $statusCode = $exception->getStatusCode();

        if ($statusCode === 403) {
            $this->logger->warning('Access denied');
        } elseif ($statusCode === 405) {
            $this->logger->warning('Method not allowed');
        } elseif ($this->httpExceptionShouldBeLogged($exception)) {
            $this->logger->critical("Code {$exception->getStatusCode()}: {$exception->getMessage()}");
        }
    }

    private function httpExceptionShouldBeLogged(HttpExceptionInterface $exception)
    {
        $exceptionCode = $exception->getStatusCode();

        return is_int($exceptionCode) && $exceptionCode < 200 || $exceptionCode >= 500;
    }
}

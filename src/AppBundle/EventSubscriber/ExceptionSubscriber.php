<?php

namespace AppBundle\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    private $logger;
    private $fileLogger;

    /**
     * ExceptionListener constructor.
     *
     * @param LoggerInterface $logger
     * @param LoggerInterface $fileLogger
     */
    public function __construct(LoggerInterface $logger, LoggerInterface $fileLogger)
    {
        $this->logger = $logger;
        $this->fileLogger = $fileLogger;
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::EXCEPTION => array(
                array('logException', 0),
            ),
        );
    }

    public function logException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $errorMsg = "```\n" .
                    "{$exception->getMessage()}\n" .
                    "```\n" .
                    "in {$exception->getFile()} (line {$exception->getLine()})";

        if ($exception instanceof HttpException) {
            $this->logHttpException($exception);
        } else {
            $this->logger->critical($errorMsg);

            $this->fileLogger->critical(
                "File: {$exception->getFile()}\n" .
                "Line: {$exception->getLine()}\n" .
                "Message: {$exception->getMessage()}\n" .
                $exception->getTraceAsString()
            );
        }
    }

    private function logHttpException(HttpException $exception)
    {
        $statusCode = $exception->getStatusCode();

        if ($statusCode === 403) {
            $this->logger->warning("Access denied");
        } elseif ($statusCode === 405) {
            $this->logger->warning("Method not allowed");
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

<?php

namespace AppBundle\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class ExceptionSubscriber implements EventSubscriberInterface
{
    private $logger;
    private $ts;
    private $router;
    private $fileLogger;
    private $requestStack;

    /**
     * ExceptionListener constructor.
     *
     * @param LoggerInterface $logger
     * @param LoggerInterface $fileLogger
     * @param TokenStorage $ts
     * @param RouterInterface $router
     * @param RequestStack $requestStack
     */
    public function __construct(LoggerInterface $logger, LoggerInterface $fileLogger, TokenStorage $ts, RouterInterface $router, RequestStack $requestStack)
    {
        $this->logger = $logger;
        $this->ts = $ts;
        $this->router = $router;
        $this->fileLogger = $fileLogger;
        $this->requestStack = $requestStack;
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
        $request = $this->requestStack->getMasterRequest();
        $path = $request ? $request->getPathInfo() : '???';

        if ($exception instanceof HttpException) {
            $this->logHttpException($exception);
        } else {
            $this->logger->critical(
                "User: {$this->getLoggedInUserName()}\n" .
                "Path: $path\n" .
                "File: {$exception->getFile()}\n" .
                "Line: {$exception->getLine()}\n" .
                "Message:\n" .
                "```\n" .
                "{$exception->getMessage()}\n" .
                "```"
            );

            $this->fileLogger->critical(
                "User: {$this->getLoggedInUserName()}\n" .
                "Path: $path\n" .
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
            $this->logger->warning(
                "Code {$exception->getStatusCode()} {$exception->getMessage()}: "
                ."{$this->getLoggedInUserName()} tried to access {$this->router->getContext()->getPathInfo()}"
            );
        } elseif ($this->httpExceptionShouldBeLogged($exception)) {
            $this->logger->critical("Code {$exception->getStatusCode()}: {$exception->getMessage()}");
        }
    }

    private function httpExceptionShouldBeLogged(HttpExceptionInterface $exception)
    {
        $exceptionCode = $exception->getStatusCode();

        return is_int($exceptionCode) && $exceptionCode < 200 || $exceptionCode >= 500;
    }

    private function getLoggedInUserName()
    {
        if ($this->ts->getToken() !== null && $this->ts->getToken()->getUser() !== null) {
            $user = $this->ts->getToken()->getUser();
            if (method_exists($user, '__toString')) {
                return $this->ts->getToken()->getUser()->__toString();
            }
        }

        return 'Anonymous User';
    }
}

<?php

namespace AppBundle\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    private $logger;

    /**
     * ExceptionListener constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
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

        $errorCode = $exception->getCode();

        if (!(is_int($errorCode) && $errorCode >= 200 && $errorCode < 500)) {
            $this->logger->critical("Code $errorCode: {$exception->getMessage()}");
        }
    }
}

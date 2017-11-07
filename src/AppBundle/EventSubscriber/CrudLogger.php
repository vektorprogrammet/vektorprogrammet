<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Entity\User;
use AppBundle\Event\CrudEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CrudLogger implements EventSubscriberInterface
{
    private $logger;
    private $tokenStorage;

    public function __construct(LoggerInterface $logger, TokenStorageInterface $tokenStorage)
    {
        $this->logger = $logger;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        $subscribedEvents = [];
        /**
         * @var CrudEvent $class
         */
        foreach (get_declared_classes() as $class) {
            if (in_array(CrudEvent::class, class_implements($class))) {
                $subscribedEvents[$class::created()] = [
                    ['logCreated', 1]
                ];
                $subscribedEvents[$class::updated()] = [
                    ['logUpdated', 1]
                ];
                $subscribedEvents[$class::deleted()] = [
                    ['logDeleted', 1]
                ];
            }
        }

        return $subscribedEvents;
    }

    public function logCreated(CrudEvent $event)
    {
        $obj = $event->getObject();
        $className = get_class($obj);
        $loggedInUser = $this->getUser();
        $name = $this->getObjectName($obj);

        $this->logger->info("$className $name created by $loggedInUser");
    }

    public function logUpdated(CrudEvent $event)
    {
        $obj = $event->getObject();
        $className = get_class($obj);
        $loggedInUser = $this->getUser();
        $name = $this->getObjectName($obj);

        $this->logger->info("$className $name updated by $loggedInUser");
    }

    public function logDeleted(CrudEvent $event)
    {
        $obj = $event->getObject();
        $className = get_class($obj);
        $loggedInUser = $this->getUser();
        $name = $this->getObjectName($obj);

        $this->logger->info("$className $name deleted by $loggedInUser");
    }
    
    private function getUser() {
        $loggedInUser = $this->tokenStorage->getToken()->getUser();
        if ($loggedInUser && $loggedInUser instanceof User) {
            return $loggedInUser->getFullName();
        }
        
        return "Anonymous";
    }

    private function getObjectName($obj) {
        $name = "";
        if (method_exists($obj, '__toString')) {
            $name = $obj->__toString();
        }

        return $name;
    }

}

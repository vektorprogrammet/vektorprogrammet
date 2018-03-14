<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DbSubscriber implements EventSubscriber
{
    private $logger;
    private $tokenStorage;
    private $request;

    public function __construct(LoggerInterface $logger, TokenStorageInterface $tokenStorage, RequestStack $request)
    {
        $this->logger = $logger;
        $this->tokenStorage = $tokenStorage;
        $this->request = $request;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            'postPersist',
            'postUpdate',
            'postRemove'
        );
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->log($args, 'updated');
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->log($args, 'created');
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $this->log($args, 'deleted');
    }

    private function log(LifecycleEventArgs $args, string $action)
    {
        $obj = $args->getObject();
        $className = get_class($obj);
        $loggedInUser = $this->getUser();
        $objName = $this->getObjectName($obj);
        $request = $this->request->getMasterRequest();
        $path = $request ? $request->getPathInfo() : '???';

        $this->logger->info("Path: `$path`\n$className $objName $action by $loggedInUser");
    }

    private function getUser()
    {
        $user = "Anonymous";
        $token = $this->tokenStorage->getToken();
        if (!$token) {
            return $user;
        }

        $loggedInUser = $this->tokenStorage->getToken()->getUser();
        if ($loggedInUser && $loggedInUser instanceof User) {
            $department = $loggedInUser->getDepartment()->getShortName();
            $user = "*{$loggedInUser->getFullName()}* ($department)";
        }

        return $user;
    }

    private function getObjectName($obj)
    {
        $name = "";
        if (method_exists($obj, '__toString')) {
            $name = "`{$obj->__toString()}`";
        }

        return $name;
    }
}

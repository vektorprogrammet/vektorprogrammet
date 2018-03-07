<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DbSubscriber implements EventSubscriber {
	private $logger;
	private $tokenStorage;

	public function __construct(LoggerInterface $logger, TokenStorageInterface $tokenStorage) {
		$this->logger = $logger;
		$this->tokenStorage = $tokenStorage;
	}

	/**
	 * Returns an array of events this subscriber wants to listen to.
	 *
	 * @return array
	 */
	public function getSubscribedEvents() {
		return array(
			'postPersist',
			'postUpdate',
			'postRemove'
		);
	}

	public function postUpdate(LifecycleEventArgs $args)
	{
		$obj = $args->getObject();
		$className = get_class($obj);
		$loggedInUser = $this->getUser();
		$name = $this->getObjectName($obj);

		$this->logger->info("$className $name updated by $loggedInUser");
	}

	public function postPersist(LifecycleEventArgs $args)
	{
		$obj = $args->getObject();
		$className = get_class($obj);
		$loggedInUser = $this->getUser();
		$name = $this->getObjectName($obj);

		$this->logger->info("$className $name created by $loggedInUser");
	}

	public function postRemove(LifecycleEventArgs $args) {
		$obj = $args->getObject();
		$className = get_class($obj);
		$loggedInUser = $this->getUser();
		$name = $this->getObjectName($obj);

		$this->logger->info("$className $name deleted by $loggedInUser");
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

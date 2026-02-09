<?php


namespace App\EventSubscriber;

use App\Service\AccessControlService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class AccessControlSubscriber implements EventSubscriberInterface
{
    /**
     * @var AccessControlService
     */
    private $accessControlService;

    /**
     * @param AccessControlService $accessControlService
     */
    public function __construct(AccessControlService $accessControlService)
    {
        $this->accessControlService = $accessControlService;
    }


    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                ["checkAccess"]
            ]
        ];
    }

    /**
     * @param RequestEvent $event
     */
    public function checkAccess(RequestEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        $route = $request->attributes->get('_route');
        $method = $event->getRequest()->getRealMethod();

        $userHasAccess = $this->accessControlService->checkAccess([$route => $method]);
        if (!$userHasAccess) {
            throw new AccessDeniedHttpException("User does not have access to $method $route");
        }
    }
}

<?php

namespace AppBundle\Service\Contract;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Interface for BetaRedirecter service.
 * Defines contract for beta redirect operations.
 */
interface BetaRedirecterInterface
{
    /**
     * Handle kernel request event for beta redirect.
     *
     * @param GetResponseEvent $event
     * @return GetResponseEvent
     */
    public function onKernelRequest(GetResponseEvent $event): GetResponseEvent;
}

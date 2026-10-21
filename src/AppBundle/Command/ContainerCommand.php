<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class ContainerCommand extends Command
{
    public function __construct(private ContainerInterface $container)
    {
        parent::__construct();
    }

    protected function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}

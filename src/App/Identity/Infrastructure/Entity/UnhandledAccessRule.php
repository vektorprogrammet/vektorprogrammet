<?php

namespace App\Identity\Infrastructure\Entity;

use App\Identity\Infrastructure\Repository\UnhandledAccessRuleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UnhandledAccessRuleRepository::class)]
#[ORM\Table(name: 'unhandled_access_rule')]
class UnhandledAccessRule implements \Stringable
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private $id;

    public function __construct(
        #[ORM\Column(type: 'string')]
        private $resource,
        #[ORM\Column(type: 'string')]
        private $method,
    ) {
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @param string $resource
     */
    public function setResource($resource): void
    {
        $this->resource = $resource;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method): void
    {
        $this->method = $method;
    }

    public function __toString(): string
    {
        return $this->getMethod().' '.$this->getResource();
    }
}

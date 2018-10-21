<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\UnhandledAccessRuleRepository")
 * @ORM\Table(name="unhandled_access_rule")
 */
class UnhandledAccessRule
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $resource;

    /**
     * @ORM\Column(type="string")
     */
    private $method;

    public function __construct($resource, $method)
    {
        $this->resource = $resource;
        $this->method = $method;
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

    public function __toString()
    {
        return $this->getMethod() . " " . $this->getResource();
    }
}

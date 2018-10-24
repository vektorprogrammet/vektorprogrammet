<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\AccessRuleRepository")
 * @ORM\Table(name="access_rule")
 */
class AccessRule
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
    private $name;

    /**
     * @ORM\Column(type="string")
     */
    private $resource;

    /**
     * @ORM\Column(type="string")
     */
    private $method;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isRoutingRule;

    /**
     * @ORM\Column(type="boolean")
     */
    private $forExecutiveBoard;

    /**
     * @ORM\ManyToMany(targetEntity="User")
     */
    private $users;

    /**
     * @ORM\ManyToMany(targetEntity="Team")
     */
    private $teams;

    /**
     * @ORM\ManyToMany(targetEntity="Role")
     */
    private $roles;

    public function __construct()
    {
        $this->isRoutingRule = false;
        $this->forExecutiveBoard = false;
        $this->method = "GET";
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
     *
     * @return AccessRule
     */
    public function setResource($resource): AccessRule
    {
        $this->resource = $resource;

        return $this;
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
     *
     * @return AccessRule
     */
    public function setMethod($method): AccessRule
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @return User[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param User[] $users
     *
     * @return AccessRule
     */
    public function setUsers($users): AccessRule
    {
        $this->users = $users;

        return $this;
    }

    /**
     * @return Team[]
     */
    public function getTeams()
    {
        return $this->teams;
    }

    /**
     * @param Team[] $teams
     *
     * @return AccessRule
     */
    public function setTeams($teams): AccessRule
    {
        $this->teams = $teams;

        return $this;
    }

    /**
     * @return Role[]
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param Role[] $roles
     *
     * @return AccessRule
     */
    public function setRoles($roles): AccessRule
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return AccessRule
     */
    public function setName($name) : AccessRule
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isRoutingRule()
    {
        return $this->isRoutingRule;
    }

    /**
     * @param boolean $isRoutingRule
     *
     * @return AccessRule
     */
    public function setIsRoutingRule($isRoutingRule): AccessRule
    {
        $this->isRoutingRule = $isRoutingRule;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isForExecutiveBoard()
    {
        return $this->forExecutiveBoard;
    }

    /**
     * @param boolean $forExecutiveBoard
     *
     * @return AccessRule
     */
    public function setForExecutiveBoard($forExecutiveBoard): AccessRule
    {
        $this->forExecutiveBoard = $forExecutiveBoard;

        return $this;
    }

    public function isEmpty()
    {
        return
            count($this->getUsers()) == 0 &&
            count($this->getTeams()) == 0 &&
            count($this->getRoles()) == 0 &&
            !$this->isForExecutiveBoard();
    }

    public function __toString()
    {
        return $this->name;
    }
}

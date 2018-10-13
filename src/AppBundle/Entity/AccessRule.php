<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\AccessRuleRepository")
 * @ORM\Table(name="access_rule")
 * @ORM\HasLifecycleCallbacks
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

	public function __construct() {
		$this->isRoutingRule = false;
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

    /**
     * @return User[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param User[] $users
     */
    public function setUsers($users): void
    {
        $this->users = $users;
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
     */
    public function setTeams($teams): void
    {
        $this->teams = $teams;
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
     */
    public function setRoles($roles): void
    {
        $this->roles = $roles;
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
     */
    public function setName($name)
    {
        $this->name = $name;
    }

	/**
	 * @return boolean
	 */
	public function isRoutingRule() {
		return $this->isRoutingRule;
	}

	/**
	 * @param boolean $isRoutingRule
	 */
	public function setIsRoutingRule( $isRoutingRule ): void {
		$this->isRoutingRule = $isRoutingRule;
	}
}

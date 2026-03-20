<?php

namespace App\Entity;

use App\Entity\Repository\AccessRuleRepository;
use App\Organization\Infrastructure\Entity\Team;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccessRuleRepository::class)]
#[ORM\Table(name: 'access_rule')]
class AccessRule implements \Stringable
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private $id;

    #[ORM\Column(type: 'string')]
    private $name;

    #[ORM\Column(type: 'string')]
    private $resource;

    #[ORM\Column(type: 'string')]
    private $method;

    #[ORM\Column(type: 'boolean')]
    private $isRoutingRule;

    #[ORM\Column(type: 'boolean')]
    private $forExecutiveBoard;

    #[ORM\ManyToMany(targetEntity: User::class)]
    private $users;

    #[ORM\ManyToMany(targetEntity: Team::class)]
    private $teams;

    #[ORM\ManyToMany(targetEntity: Role::class)]
    private $roles;

    public function __construct()
    {
        $this->isRoutingRule = false;
        $this->forExecutiveBoard = false;
        $this->method = 'GET';
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
     */
    public function setName($name): AccessRule
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRoutingRule()
    {
        return $this->isRoutingRule;
    }

    /**
     * @param bool $isRoutingRule
     */
    public function setIsRoutingRule($isRoutingRule): AccessRule
    {
        $this->isRoutingRule = $isRoutingRule;

        return $this;
    }

    /**
     * @return bool
     */
    public function isForExecutiveBoard()
    {
        return $this->forExecutiveBoard;
    }

    /**
     * @param bool $forExecutiveBoard
     */
    public function setForExecutiveBoard($forExecutiveBoard): AccessRule
    {
        $this->forExecutiveBoard = $forExecutiveBoard;

        return $this;
    }

    public function isEmpty()
    {
        $users = $this->getUsers();
        $teams = $this->getTeams();
        $roles = $this->getRoles();

        return
            count(is_countable($users) ? $users : []) == 0
            && count(is_countable($teams) ? $teams : []) == 0
            && count(is_countable($roles) ? $roles : []) == 0
            && !$this->isForExecutiveBoard();
    }

    public function __toString(): string
    {
        return (string) $this->name;
    }
}

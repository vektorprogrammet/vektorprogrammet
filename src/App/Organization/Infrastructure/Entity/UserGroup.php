<?php

declare(strict_types=1);

namespace App\Organization\Infrastructure\Entity;

use App\Identity\Infrastructure\Entity\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'usergroup')]
class UserGroup implements \Stringable
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private $id;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', nullable: false)]
    private $name;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean')]
    private $active;

    /**
     * @var User[]
     */
    #[ORM\ManyToMany(targetEntity: User::class)]
    private $users;

    /**
     * @var UserGroupCollection
     */
    #[ORM\ManyToOne(targetEntity: UserGroupCollection::class, inversedBy: 'userGroups', cascade: ['persist'])]
    #[ORM\JoinColumn]
    private $userGroupCollection;

    public function __construct()
    {
        $this->users = [];
        $this->name = '';
        $this->active = false;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
    public function setUsers(array $users): void
    {
        $this->users = $users;
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
    public function setName($name): void
    {
        $this->name = $name;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getUserGroupCollection(): UserGroupCollection
    {
        return $this->userGroupCollection;
    }

    public function setUserGroupCollection(UserGroupCollection $userGroupCollection): void
    {
        $this->userGroupCollection = $userGroupCollection;
    }
}

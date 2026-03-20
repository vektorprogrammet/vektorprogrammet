<?php

namespace App\Identity\Infrastructure\Entity;

use App\Identity\Infrastructure\Repository\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'role')]
#[ORM\Entity(repositoryClass: RoleRepository::class)]
class Role implements \Stringable
{
    #[ORM\Column(name: 'id', type: 'integer', length: 11)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private $id;

    #[ORM\Column(name: 'name', type: 'string', length: 30)]
    private $name;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'roles')]
    private $users;

    public function __construct(#[ORM\Column(name: 'role', type: 'string', length: 20)]
        private string $role = '')
    {
        $this->users = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->getRole();
    }

    /**
     * @see RoleInterface
     */
    public function getRole()
    {
        return $this->role;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setRole($role)
    {
        $this->role = $role;
    }

    public function setUsers($users)
    {
        $this->users = $users;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add users.
     *
     * @return Role
     */
    public function addUser(User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users.
     */
    public function removeUser(User $users)
    {
        $this->users->removeElement($users);
    }
}

<?php

namespace AppBundle\Entity;

use Symfony\Component\Security\Core\Role\RoleInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="role")
 * @ORM\Entity()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\RoleRepository")
 */
class Role implements RoleInterface {

    /**
     * @ORM\Column(name="id", type="integer", length=11)
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=30)
     */
    private $name;

    /**
     * @ORM\Column(name="role", type="string", length=20)
     */
    private $role;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="roles")
     */
    private $users;

    public function __construct() {
        $this->users = new ArrayCollection();
    }

    /**
     * @see RoleInterface
     */
    public function getRole() {
        return $this->role;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setRole($role) {
        $this->role = $role;
    }

    function setUsers($users) {
        $this->users = $users;
    }

    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function getUsers() {
        return $this->users;
    }


    /**
     * Add users
     *
     * @param \AppBundle\Entity\User $users
     * @return Role
     */
    public function addUser(\AppBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \AppBundle\Entity\User $users
     */
    public function removeUser(\AppBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }
}

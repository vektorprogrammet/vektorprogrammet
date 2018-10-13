<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\SchoolRepository")
 * @ORM\Table(name="school")
 */
class School
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     */
    protected $contactPerson;

    /**
     * @ORM\ManyToMany(targetEntity="Department", mappedBy="schools")
     * @ORM\JoinColumn(onDelete="cascade")
     **/
    protected $departments;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     * @Assert\Email(message="Ikke gyldig e-post.")
     */
    protected $email;

    /**
     * @ORM\OneToMany(targetEntity="AssistantHistory", mappedBy="school")
     */
    private $assistantHistories;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     */
    protected $phone;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     * @Assert\NotBlank(message="Dette feltet kan ikke være tomt.")
     */
    private $international;

    /**
     * @var SchoolCapacity[]
     * @ORM\OneToMany(targetEntity="SchoolCapacity", mappedBy="school")
     */
    private $capacities;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", options={"default": 1})
     */
    private $active;

    public function __construct()
    {
        $this->departments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->international = false;
        $this->active = true;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return School
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set contactPerson.
     *
     * @param string $contactPerson
     *
     * @return School
     */
    public function setContactPerson($contactPerson)
    {
        $this->contactPerson = $contactPerson;

        return $this;
    }

    /**
     * Get contactPerson.
     *
     * @return string
     */
    public function getContactPerson()
    {
        return $this->contactPerson;
    }

    /**
     * Add departments.
     *
     * @param \AppBundle\Entity\Department $departments
     *
     * @return School
     */
    public function addDepartment(\AppBundle\Entity\Department $departments)
    {
        $this->departments[] = $departments;

        return $this;
    }

    /**
     * Remove departments.
     *
     * @param \AppBundle\Entity\Department $departments
     */
    public function removeDepartment(\AppBundle\Entity\Department $departments)
    {
        $this->departments->removeElement($departments);
    }

    /**
     * Get departments.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDepartments()
    {
        return $this->departments;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return School
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone.
     *
     * @param string $phone
     *
     * @return School
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone.
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    public function __toString()
    {
        return $this->getName();
    }

    // Used for unit testing
    public function fromArray($data = array())
    {
        foreach ($data as $property => $value) {
            $method = "set{$property}";
            $this->$method($value);
        }
    }

    /**
     * @return bool
     */
    public function isInternational()
    {
        return $this->international;
    }

    /**
     * @param bool $international
     */
    public function setInternational($international)
    {
        $this->international = $international;
    }

    /**
     * @param Department $department
     *
     * @return bool
     */
    public function belongsToDepartment(Department $department): bool
    {
        foreach ($this->departments as $dep) {
            if ($dep === $department) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return AssistantHistory[]
     */
    public function getAssistantHistories()
    {
        return $this->assistantHistories;
    }

    /**
     * @return SchoolCapacity[]
     */
    public function getCapacities(): array
    {
        return $this->capacities;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return School
     */
    public function setActive(bool $active): School
    {
        $this->active = $active;
        return $this;
    }
}

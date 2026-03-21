<?php

namespace App\Scheduling\Infrastructure\Entity;

use App\Organization\Infrastructure\Entity\Department;
use App\Scheduling\Infrastructure\Repository\SchoolRepository;
use App\Operations\Infrastructure\Entity\AssistantHistory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SchoolRepository::class)]
#[ORM\Table(name: 'school')]
class School implements \Stringable
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    protected $name;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    protected $contactPerson;
    #[ORM\ManyToMany(targetEntity: Department::class, mappedBy: 'schools')]
    #[ORM\JoinColumn(onDelete: 'cascade')]
    protected $departments;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    #[Assert\Email(message: 'Ikke gyldig e-post.')]
    protected $email;

    #[ORM\OneToMany(targetEntity: AssistantHistory::class, mappedBy: 'school')]
    private $assistantHistories;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    protected $phone;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    private $international;

    #[ORM\OneToMany(targetEntity: SchoolCapacity::class, mappedBy: 'school')]
    private $capacities;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean', options: ['default' => 1])]
    private $active;

    public function __construct()
    {
        $this->departments = new ArrayCollection();
        $this->assistantHistories = new ArrayCollection();
        $this->capacities = new ArrayCollection();
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
     * @return School
     */
    public function addDepartment(Department $departments)
    {
        $this->departments[] = $departments;

        return $this;
    }

    /**
     * Remove departments.
     */
    public function removeDepartment(Department $departments)
    {
        $this->departments->removeElement($departments);
    }

    /**
     * Get departments.
     *
     * @return Collection
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

    public function __toString(): string
    {
        return $this->getName();
    }

    // Used for unit testing
    public function fromArray($data = [])
    {
        foreach ($data as $property => $value) {
            $method = "set{$property}";
            // @phpstan-ignore method.dynamicName
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

    public function belongsToDepartment(Department $department): bool
    {
        foreach ($this->departments as $dep) {
            if ($dep->getId() === $department->getId()) {
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
    public function getCapacities()
    {
        return $this->capacities;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): School
    {
        $this->active = $active;

        return $this;
    }
}

<?php

namespace App\Operations\Infrastructure\Entity;

use App\Entity\Department;
use App\Scheduling\Infrastructure\Entity\School;
use App\Entity\User;
use App\Operations\Infrastructure\Repository\AssistantHistoryRepository;
use App\Shared\Entity\Semester;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'assistant_history')]
#[ORM\Entity(repositoryClass: AssistantHistoryRepository::class)]
class AssistantHistory implements \Stringable
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private $id;
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'assistantHistories')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private $user;
    #[ORM\ManyToOne(targetEntity: Semester::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    private $semester;

    /**
     * @var Department
     */
    #[ORM\ManyToOne(targetEntity: Department::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[Assert\NotBlank(message: 'Region må velges.')]
    private $department;
    #[ORM\ManyToOne(targetEntity: School::class, inversedBy: 'assistantHistories')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    private $school;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    private $workdays;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    private $bolk;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    private $day;

    public function activeInGroup($group): bool
    {
        return str_contains((string) $this->bolk, "Bolk $group");
    }

    /**
     * Set user.
     *
     * @return AssistantHistory
     */
    public function setUser(?User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set semester.
     *
     * @return AssistantHistory
     */
    public function setSemester(?Semester $semester = null)
    {
        $this->semester = $semester;

        return $this;
    }

    /**
     * Get semester.
     *
     * @return Semester
     */
    public function getSemester()
    {
        return $this->semester;
    }

    /**
     * @return Department
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * @return AssistantHistory
     */
    public function setDepartment(Department $department)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Set school.
     *
     * @return AssistantHistory
     */
    public function setSchool(?School $school = null)
    {
        $this->school = $school;

        return $this;
    }

    /**
     * Get school.
     *
     * @return School
     */
    public function getSchool()
    {
        return $this->school;
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

    public function __toString(): string
    {
        return (string) $this->getId();
    }

    /**
     * Set workdays.
     *
     * @param string $workdays
     *
     * @return AssistantHistory
     */
    public function setWorkdays($workdays)
    {
        $this->workdays = $workdays;

        return $this;
    }

    /**
     * Get workdays.
     *
     * @return string
     */
    public function getWorkdays()
    {
        return $this->workdays;
    }

    /**
     * @return string
     */
    public function getBolk()
    {
        return $this->bolk;
    }

    /**
     * @param string $bolk
     */
    public function setBolk($bolk)
    {
        $this->bolk = $bolk;
    }

    /**
     * @return string
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @param string $day
     */
    public function setDay($day)
    {
        $this->day = $day;
    }

    // Used for unit testing
    public function fromArray($data = [])
    {
        foreach ($data as $property => $value) {
            $method = "set{$property}";
            $this->$method($value);
        }
    }
}

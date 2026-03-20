<?php

namespace App\Content\Infrastructure\Entity;

use App\Content\Infrastructure\Repository\SocialEventRepository;
use App\Organization\Infrastructure\Entity\Department;
use App\Identity\Infrastructure\Entity\Role;
use App\Shared\Entity\Semester;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'event')]
#[ORM\Entity(repositoryClass: SocialEventRepository::class)]
class SocialEvent
{
    #[ORM\ManyToOne(targetEntity: Department::class)]
    private $department;

    /**
     * @var Semester
     */
    #[ORM\ManyToOne(targetEntity: Semester::class)]
    #[ORM\JoinColumn(referencedColumnName: 'id')]
    private $semester;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private $id;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private $title;

    #[ORM\Column(type: 'string', length: 5000)]
    private $description;

    #[ORM\Column(type: 'datetime')]
    private $startTime;

    #[ORM\Column(type: 'datetime')]
    private $endTime;

    /**
     * @var Role
     */
    #[ORM\ManyToOne(targetEntity: Role::class)]
    #[ORM\JoinColumn(referencedColumnName: 'id')]
    private $role;

    #[ORM\Column(type: 'string', length: 250, nullable: true)]
    #[Assert\Length(max: 250)]
    private $link;

    /**
     * Constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink($link): SocialEvent
    {
        $this->link = $link;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title): SocialEvent
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description): SocialEvent
    {
        $this->description = $description;

        return $this;
    }

    public function getStartTime(): ?\DateTime
    {
        return $this->startTime;
    }

    /**
     * @param \DateTime $startTime
     */
    public function setStartTime($startTime): SocialEvent
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTime
    {
        return $this->endTime;
    }

    /**
     * @param \DateTime $endTime
     */
    public function setEndTime($endTime): SocialEvent
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): SocialEvent
    {
        $this->department = $department;

        return $this;
    }

    /**
     * @return $this
     */
    public function setSemester(?Semester $semester)
    {
        $this->semester = $semester;

        return $this;
    }

    public function getSemester(): ?Semester
    {
        return $this->semester;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(Role $role): SocialEvent
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function hasHappened(): bool
    {
        return $this->getStartTime() < new \DateTime();
    }

    /**
     * @throws \Exception
     */
    public function happensSoon(): bool
    {
        return !$this->hasHappened() && $this->getStartTime() < new \DateTime('+1 week');
    }
}

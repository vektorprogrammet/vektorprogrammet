<?php

namespace App\Entity;

use App\Entity\Repository\SemesterRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'semester')]
#[ORM\Entity(repositoryClass: SemesterRepository::class)]
class Semester implements PeriodInterface, \Stringable
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[Groups(['admission:read'])]
    private $id;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    private $semesterTime;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    private $year;

    /**
     * @var AdmissionPeriod[]
     */
    #[ORM\OneToMany(targetEntity: AdmissionPeriod::class, mappedBy: 'semester')]
    private $admissionPeriods;

    public function __toString(): string
    {
        return $this->getName();
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
     * Get name.
     *
     * @return string
     */
    #[Groups(['admission:read'])]
    public function getName()
    {
        return $this->semesterTime.' '.$this->year;
    }

    /**
     * Get semester start date.
     */
    public function getStartDate(): \DateTime
    {
        $startMonth = $this->semesterTime == 'Vår' ? '01' : '08';

        return date_create($this->year.'-'.$startMonth.'-01 00:00:00');
    }

    /**
     * Get semester end date.
     */
    public function getEndDate(): \DateTime
    {
        $endMonth = $this->semesterTime == 'Vår' ? '07' : '12';

        return date_create($this->year.'-'.$endMonth.'-31 23:59:59');
    }

    // Used for unit testing
    public function fromArray($data = [])
    {
        foreach ($data as $property => $value) {
            $method = "set{$property}";
            $this->$method($value);
        }
    }

    /**
     * @return string
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param string $year
     */
    public function setYear($year): Semester
    {
        $this->year = $year;

        return $this;
    }

    /**
     * @return string
     */
    public function getSemesterTime()
    {
        return $this->semesterTime;
    }

    /**
     * @param string $semesterTime
     *
     * @return Semester
     */
    public function setSemesterTime($semesterTime)
    {
        $this->semesterTime = $semesterTime;

        return $this;
    }

    public function isActive(): bool
    {
        $now = new \DateTime();

        return $this->getStartDate() < $now && $now <= $this->getEndDate();
    }

    /**
     * @return AdmissionPeriod[]
     */
    public function getAdmissionPeriods()
    {
        return $this->admissionPeriods;
    }

    /**
     * @param AdmissionPeriod $admissionPeriods
     *
     * @return Semester
     */
    public function setAdmissionPeriods($admissionPeriods)
    {
        $this->admissionPeriods = $admissionPeriods;

        return $this;
    }

    /**
     * Checks if this semester is between the bounds $semesterPrevious and $semesterLater.
     *
     * **Note**: This range comparison is weak, meaning the semester can count as
     * being inBetween even though it is equal to one or both of the semester
     * bounds.
     * Furthermore, the semester bounds can be null, which implies the range
     * extends infinitely far into the past or into the future.
     */
    public function isBetween(?Semester $semesterPrevious, ?Semester $semesterLater): bool
    {
        return $this->isAfter($semesterPrevious) && $this->isBefore($semesterLater);
    }

    /**
     * Checks if this semester is before $semester.
     *
     * **Note**: This function performs a weak comparison, meaning equal semesters count as before.
     * Furthermore, null semesters also count as before
     */
    public function isBefore(?Semester $semester): bool
    {
        if ($semester === null) {
            return true;
        }
        if ($this->year === $semester->getYear()) {
            return !($this->semesterTime === 'Høst'
                     && $semester->getSemesterTime() === 'Vår');
        }

        return $this->year < $semester->getYear();
    }

    /**
     * Checks if this semester is after $semester.
     *
     * **Note**: This function performs a weak comparison, meaning equal semesters count as after.
     * Furthermore, null semesters also count as after
     */
    public function isAfter(?Semester $semester): bool
    {
        if ($semester === null) {
            return true;
        }
        if ($this->year === $semester->getYear()) {
            return !($this->semesterTime === 'Vår'
                     && $semester->getSemesterTime() === 'Høst');
        }

        return $this->year > $semester->getYear();
    }
}

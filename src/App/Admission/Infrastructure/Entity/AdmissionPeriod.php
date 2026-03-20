<?php

namespace App\Admission\Infrastructure\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Admission\Infrastructure\Repository\AdmissionPeriodRepository;
use App\Organization\Infrastructure\Entity\Department;
use App\Shared\Contracts\PeriodInterface;
use App\Support\Utils\TimeUtil;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Shared\Entity\Semester;

/**
 * DepartmentSpecificSemester.
 */
#[ORM\Table]
#[ORM\UniqueConstraint(name: 'unique_department_semester', columns: ['department_id', 'semester_id'])]
#[ORM\Entity(repositoryClass: AdmissionPeriodRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
    ],
    normalizationContext: ['groups' => ['admission:read']],
)]
class AdmissionPeriod implements PeriodInterface, \Stringable
{
    /**
     * @var int
     */
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[Groups(['admission:read'])]
    private $id;

    /**
     * @var Department
     */
    #[ORM\ManyToOne(targetEntity: Department::class, inversedBy: 'admissionPeriods')]
    #[Groups(['admission:read'])]
    private $department;

    #[ORM\Column(name: 'start_date', type: 'datetime', length: 150)]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    #[Groups(['admission:read'])]
    private $startDate;

    #[ORM\Column(name: 'end_date', type: 'datetime', length: 150)]
    #[Assert\NotBlank(message: 'Dette feltet kan ikke være tomt.')]
    #[Groups(['admission:read'])]
    private $endDate;

    /**
     * @var InfoMeeting
     */
    #[ORM\OneToOne(targetEntity: InfoMeeting::class, cascade: ['remove', 'persist'])]
    #[Assert\Valid]
    #[Groups(['admission:read', 'department:detail'])]
    private $infoMeeting;

    /**
     * @var Semester
     */
    #[ORM\ManyToOne(targetEntity: Semester::class, inversedBy: 'admissionPeriods')]
    #[Groups(['admission:read'])]
    private $semester;

    public function __toString(): string
    {
        return $this->semester->getName().' - '.$this->getDepartment();
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
     * Set department.
     *
     * @return AdmissionPeriod
     */
    public function setDepartment(?Department $department = null)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department.
     *
     * @return Department
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Set startDate.
     *
     * @param \DateTime $startDate
     *
     * @return AdmissionPeriod
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate.
     */
    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    /**
     * Set endDate.
     *
     * @param \DateTime $endDate
     *
     * @return AdmissionPeriod
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate.
     */
    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    /**
     * @return InfoMeeting|null
     */
    public function getInfoMeeting()
    {
        return $this->infoMeeting;
    }

    /**
     * @param InfoMeeting $infoMeeting
     */
    public function setInfoMeeting($infoMeeting)
    {
        $this->infoMeeting = $infoMeeting;
    }

    public function isActive(): bool
    {
        $now = new \DateTime();

        return $this->semester->getStartDate() < $now && $now <= $this->semester->getEndDate();
    }

    public function hasActiveAdmission(): bool
    {
        $now = new \DateTime();

        return $this->getStartDate() <= $now && $now <= $this->getEndDate();
    }

    /**
     * @return Semester
     */
    public function getSemester()
    {
        return $this->semester;
    }

    /**
     * @param Semester $semester
     *
     * @return AdmissionPeriod
     */
    public function setSemester($semester)
    {
        $this->semester = $semester;

        return $this;
    }

    public function shouldSendInfoMeetingNotifications()
    {
        return $this->infoMeeting !== null
            && $this->infoMeeting->getDate() !== null
            && $this->infoMeeting->isShowOnPage()
            && TimeUtil::dateTimeIsToday($this->infoMeeting->getDate())
            && TimeUtil::dateTimeIsInTheFuture($this->infoMeeting->getDate());
    }
}

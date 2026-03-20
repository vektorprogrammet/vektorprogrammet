<?php

namespace App\Entity;

use App\Admission\Infrastructure\Entity\AdmissionPeriod;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Repository\DepartmentRepository;
use App\Scheduling\Infrastructure\Entity\School;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'department')]
#[ORM\Entity(repositoryClass: DepartmentRepository::class)]
#[UniqueEntity(fields: ['city'])]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(normalizationContext: ['groups' => ['department:read', 'department:detail']]),
    ],
    normalizationContext: ['groups' => ['department:read']],
)]
class Department implements \Stringable
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[Groups(['department:read', 'admission:read', 'field_of_study:read'])]
    private $id;

    #[ORM\Column(type: 'string', length: 250)]
    #[Assert\NotBlank]
    #[Groups(['department:read', 'admission:read'])]
    private $name;

    #[ORM\Column(name: 'short_name', type: 'string', length: 50)]
    #[Assert\NotBlank]
    #[Groups(['department:read'])]
    private $shortName;

    #[ORM\Column(type: 'string', length: 250)]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Groups(['department:read'])]
    private $email;

    #[ORM\Column(type: 'string', length: 250, nullable: true)]
    #[Groups(['department:read'])]
    protected $address;

    #[ORM\Column(type: 'string', length: 250, unique: true)]
    #[Assert\NotBlank]
    #[Groups(['department:read', 'admission:read', 'field_of_study:read'])]
    private $city;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\Length(max: 255)]
    #[Groups(['department:read'])]
    private $latitude;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\Length(max: 255)]
    #[Groups(['department:read'])]
    private $longitude;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private $slackChannel;
    #[ORM\ManyToMany(targetEntity: School::class, inversedBy: 'departments')]
    #[ORM\JoinTable(name: 'department_school')]
    #[ORM\JoinColumn(onDelete: 'cascade')]
    protected $schools;

    #[ORM\OneToMany(targetEntity: FieldOfStudy::class, mappedBy: 'department', cascade: ['remove'])]
    #[Groups(['department:detail'])]
    private $fieldOfStudy;
    #[ORM\OneToMany(targetEntity: AdmissionPeriod::class, mappedBy: 'department', cascade: ['remove'])]
    #[ORM\OrderBy(['startDate' => 'DESC'])]
    #[Groups(['department:detail'])]
    private $admissionPeriods;
    #[ORM\OneToMany(targetEntity: Team::class, mappedBy: 'department', cascade: ['remove'])]
    #[Groups(['department:detail'])]
    private $teams;
    #[ORM\Column(name: 'logo_path', type: 'string', length: 255, nullable: true)]
    #[Assert\Length(min: 1, max: 255, maxMessage: 'Path kan maks være 255 tegn.')]
    #[Groups(['department:read'])]
    private $logoPath;

    #[ORM\Column(name: 'active', type: 'boolean', nullable: false, options: ['default' => 1])]
    #[Groups(['department:read'])]
    private $active;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->schools = new ArrayCollection();
        $this->fieldOfStudy = new ArrayCollection();
        $this->admissionPeriods = new ArrayCollection();
        $this->teams = new ArrayCollection();
        $this->active = true;
    }

    /**
     * @return AdmissionPeriod
     */
    public function getCurrentAdmissionPeriod()
    {
        $now = new \DateTime();

        /** @var AdmissionPeriod $admissionPeriod */
        foreach ($this->admissionPeriods as $admissionPeriod) {
            if ($now > $admissionPeriod->getSemester()->getStartDate() && $now < $admissionPeriod->getSemester()->getEndDate()) {
                return $admissionPeriod;
            }
        }

        return null;
    }

    /**
     * @return AdmissionPeriod
     */
    public function getLatestAdmissionPeriod()
    {
        /** @var AdmissionPeriod[] $admissionPeriods */
        $admissionPeriods = $this->getAdmissionPeriods()->toArray();

        $latestAdmissionPeriod = current($admissionPeriods);

        $now = new \DateTime();

        foreach ($admissionPeriods as $admissionPeriod) {
            if ($admissionPeriod->getSemester()->getStartDate() < $now
                && $admissionPeriod->getSemester()->getEndDate() > $latestAdmissionPeriod->getSemester()->getEndDate()) {
                $latestAdmissionPeriod = $admissionPeriod;
            }
        }

        return $latestAdmissionPeriod;
    }

    /**
     * @return AdmissionPeriod
     */
    public function getCurrentOrLatestAdmissionPeriod()
    {
        if (null === $admissionPeriod = $this->getCurrentAdmissionPeriod()) {
            $admissionPeriod = $this->getLatestAdmissionPeriod();
        }

        return $admissionPeriod;
    }

    public function activeAdmission(): bool
    {
        $admissionPeriod = $this->getCurrentAdmissionPeriod();
        if (!$admissionPeriod) {
            return false;
        }

        $start = $admissionPeriod->getStartDate();
        $end = $admissionPeriod->getEndDate();
        $now = new \DateTime();

        return $start < $now && $now < $end;
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
     * @return Department
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
     * Set shortName.
     *
     * @param string $shortName
     *
     * @return Department
     */
    public function setShortName($shortName)
    {
        $this->shortName = $shortName;

        return $this;
    }

    /**
     * Get shortName.
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * Add fieldOfStudy.
     *
     * @return Department
     */
    public function addFieldOfStudy(FieldOfStudy $fieldOfStudy)
    {
        $this->fieldOfStudy[] = $fieldOfStudy;

        return $this;
    }

    /**
     * Remove fieldOfStudy.
     */
    public function removeFieldOfStudy(FieldOfStudy $fieldOfStudy)
    {
        $this->fieldOfStudy->removeElement($fieldOfStudy);
    }

    /**
     * Get fieldOfStudy.
     *
     * @return ArrayCollection
     */
    public function getFieldOfStudy()
    {
        return $this->fieldOfStudy;
    }

    public function __toString(): string
    {
        return (string) $this->getCity();
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return Department
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
     * Add schools.
     *
     * @return Department
     */
    public function addSchool(School $schools)
    {
        $this->schools[] = $schools;

        return $this;
    }

    /**
     * Remove schools.
     */
    public function removeSchool(School $schools)
    {
        $this->schools->removeElement($schools);
    }

    /**
     * Get schools.
     *
     * @return ArrayCollection
     */
    public function getSchools()
    {
        return $this->schools;
    }

    /**
     * Set address.
     *
     * @param string $address
     *
     * @return Department
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address.
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Add admission periods.
     *
     * @return Department
     */
    public function addAdmissionPeriod(AdmissionPeriod $admissionPeriod)
    {
        $this->admissionPeriods[] = $admissionPeriod;

        return $this;
    }

    /**
     * Get admission periods.
     *
     * @return ArrayCollection
     */
    public function getAdmissionPeriods()
    {
        return $this->admissionPeriods;
    }

    /**
     * Add teams.
     *
     * @return Department
     */
    public function addTeam(Team $teams)
    {
        $this->teams[] = $teams;

        return $this;
    }

    /**
     * Remove teams.
     */
    public function removeTeam(Team $teams)
    {
        $this->teams->removeElement($teams);
    }

    /**
     * Get teams.
     *
     * @return ArrayCollection
     */
    public function getTeams()
    {
        return $this->teams;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param string $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param string $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
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
    public function getSlackChannel()
    {
        return $this->slackChannel;
    }

    /**
     * @param string $slackChannel
     */
    public function setSlackChannel($slackChannel)
    {
        $this->slackChannel = $slackChannel;
    }

    /**
     * @return string
     */
    public function getLogoPath()
    {
        return $this->logoPath;
    }

    /**
     * @param string $logoPath
     */
    public function setLogoPath($logoPath)
    {
        $this->logoPath = $logoPath;
    }

    /**
     * @return bool $active
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }
}

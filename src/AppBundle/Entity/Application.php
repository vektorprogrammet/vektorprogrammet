<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as CustomAssert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\ApplicationRepository")
 * @ORM\Table(name="application")
 * @CustomAssert\ApplicationEmail(groups={"admission"})
 */
class Application implements DepartmentSemesterInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var AdmissionPeriod
     * @ORM\ManyToOne(targetEntity="AdmissionPeriod")
     */
    private $admissionPeriod;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank(groups={"admission", "admission_existing"}, message="Dette feltet kan ikke være tomt.")
     */
    private $yearOfStudy;

    /**
     * @ORM\Column(type="boolean", options={"default"=true}))
     * @var bool
     */
    private $monday;

    /**
     * @ORM\Column(type="boolean", options={"default"=true}))
     * @var bool
     */
    private $tuesday;

    /**
     * @ORM\Column(type="boolean", options={"default"=true}))
     * @var bool
     */
    private $wednesday;

    /**
     * @ORM\Column(type="boolean", options={"default"=true}))
     * @var bool
     */
    private $thursday;

    /**
     * @ORM\Column(type="boolean", options={"default"=true}))
     * @var bool
     */
    private $friday;

    /**
     * @ORM\Column(type="boolean", options={"default"=false})
     */
    private $substitute;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(groups={"interview", "admission_existing"}, message="Dette feltet kan ikke være tomt.")
     */
    private $language;

    /**
     * @ORM\Column(type="boolean", options={"default"=false})
     * @Assert\NotBlank(groups={"interview", "admission_existing"}, message="Dette feltet kan ikke være tomt.")
     */
    private $doublePosition;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $preferredGroup;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Length(max=255, maxMessage="Dette feltet kan ikke inneholde mer enn 255 tegn.")
     */
    private $preferredSchool;

    /**
     * @ORM\ManyToOne(targetEntity="User", cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\Valid
     */
    private $user;

    /**
     * @ORM\Column(type="boolean")
     */
    private $previousParticipation;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $last_edited;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created;

    /**
     * @ORM\Column(type="array")
     */
    private $heardAboutFrom;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotBlank(groups={"interview", "admission_existing"}, message="Dette feltet kan ikke være tomt.")
     */
    private $teamInterest;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Team", inversedBy="potentialMembers")
     */
    private $potentialTeams;

    /**
     * @var Interview
     * @ORM\OneToOne(targetEntity="Interview", cascade={"persist", "remove"}, inversedBy="application")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @Assert\Valid
     */
    private $interview;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $specialNeeds;

    /**
     * ApplicationInfo constructor.
     */
    public function __construct()
    {
        $this->last_edited = new DateTime();
        $this->created = new DateTime();
        $this->substitute = false;
        $this->doublePosition = false;
        $this->previousParticipation = false;
        $this->teamInterest = false;
        $this->specialNeeds = '';
        $this->monday = true;
        $this->tuesday = true;
        $this->wednesday = true;
        $this->thursday = true;
        $this->friday = true;
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
     * @return AdmissionPeriod
     */
    public function getAdmissionPeriod()
    {
        return $this->admissionPeriod;
    }

    /**
     * @param AdmissionPeriod $admissionPeriod
     *
     * @return Application
     */
    public function setAdmissionPeriod($admissionPeriod)
    {
        $this->admissionPeriod = $admissionPeriod;
        return $this;
    }

    /**
     * @return string
     */
    public function getYearOfStudy()
    {
        return $this->yearOfStudy;
    }

    /**
     * @param string $yearOfStudy
     */
    public function setYearOfStudy($yearOfStudy)
    {
        $this->yearOfStudy = $yearOfStudy;
    }

    /**
     * @return bool
     */
    public function isMonday(): bool
    {
        return $this->monday;
    }

    /**
     * @param bool $monday
     */
    public function setMonday(bool $monday)
    {
        $this->monday = $monday;
    }

    /**
     * @return bool
     */
    public function isTuesday(): bool
    {
        return $this->tuesday;
    }

    /**
     * @param bool $tuesday
     */
    public function setTuesday(bool $tuesday)
    {
        $this->tuesday = $tuesday;
    }

    /**
     * @return bool
     */
    public function isWednesday(): bool
    {
        return $this->wednesday;
    }

    /**
     * @param bool $wednesday
     */
    public function setWednesday(bool $wednesday)
    {
        $this->wednesday = $wednesday;
    }

    /**
     * @return bool
     */
    public function isThursday(): bool
    {
        return $this->thursday;
    }

    /**
     * @param bool $thursday
     */
    public function setThursday(bool $thursday)
    {
        $this->thursday = $thursday;
    }

    /**
     * @return bool
     */
    public function isFriday(): bool
    {
        return $this->friday;
    }

    /**
     * @param bool $friday
     */
    public function setFriday(bool $friday)
    {
        $this->friday = $friday;
    }


    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param \AppBundle\Entity\User
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return bool
     */
    public function getDoublePosition()
    {
        return $this->doublePosition;
    }

    /**
     * @param bool $doublePosition
     */
    public function setDoublePosition($doublePosition)
    {
        $this->doublePosition = $doublePosition;
    }

    /**
     * @return string
     */
    public function getPreferredGroup()
    {
        return $this->preferredGroup;
    }

    /**
     * @param string $preferredGroup
     */
    public function setPreferredGroup($preferredGroup)
    {
        $this->preferredGroup = $preferredGroup;
    }

    /**
     * @return DateTime
     */
    public function getLastEdited()
    {
        return $this->last_edited;
    }

    /**
     * @param DateTime $last_edited
     */
    public function setLastEdited($last_edited)
    {
        $this->last_edited = $last_edited;
    }

    /**
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * Get heardAboutFrom.
     *
     * @return array
     */
    public function getHeardAboutFrom()
    {
        return $this->heardAboutFrom;
    }

    /**
     * Set heardAboutFrom.
     *
     * @param array $heardAboutFrom
     */
    public function setHeardAboutFrom($heardAboutFrom)
    {
        $this->heardAboutFrom = $heardAboutFrom;
    }

    /**
     * @return Interview
     */
    public function getInterview()
    {
        return $this->interview;
    }

    /**
     * @param Interview $interview
     */
    public function setInterview($interview)
    {
        $this->interview = $interview;
    }

    /**
     * @return bool
     */
    public function getPreviousParticipation()
    {
        return $this->previousParticipation;
    }

    /**
     * @param bool $previousParticipation
     */
    public function setPreviousParticipation($previousParticipation)
    {
        $this->previousParticipation = $previousParticipation;
    }

    /**
     * @return bool
     */
    public function getTeamInterest()
    {
        return $this->teamInterest;
    }

    /**
     * @param bool $teamInterest
     */
    public function setTeamInterest($teamInterest)
    {
        $this->teamInterest = $teamInterest;
    }

    /**
     * @return bool
     */
    public function isSubstitute()
    {
        return $this->substitute;
    }

    /**
     * @param bool $substitute
     */
    public function setSubstitute($substitute)
    {
        $this->substitute = $substitute;
    }

    /**
     * @return string
     */
    public function getSpecialNeeds()
    {
        return $this->specialNeeds;
    }

    /**
     * @param string $specialNeeds
     */
    public function setSpecialNeeds($specialNeeds)
    {
        $this->specialNeeds = $specialNeeds;
    }

    /**
     * @return mixed
     */
    public function getPreferredSchool()
    {
        return $this->preferredSchool;
    }

    /**
     * @param mixed $preferredSchool
     */
    public function setPreferredSchool($preferredSchool): void
    {
        $this->preferredSchool = $preferredSchool;
    }

    /**
     * @return Team[]
     */
    public function getPotentialTeams()
    {
        return $this->potentialTeams;
    }

    /**
     * @param Team[] $potentialTeams
     */
    public function setPotentialTeams($potentialTeams)
    {
        $this->potentialTeams = $potentialTeams;
    }

    public function getSemester()
    {
        return $this->admissionPeriod->getSemester();
    }

    public function getDepartment()
    {
        return $this->admissionPeriod->getDepartment();
    }
}
